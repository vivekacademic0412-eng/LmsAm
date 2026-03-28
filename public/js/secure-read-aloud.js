(function () {
    const roots = Array.from(document.querySelectorAll('[data-read-aloud]'));
    if (!roots.length) {
        return;
    }

    const supportsSpeech = 'speechSynthesis' in window && typeof window.SpeechSynthesisUtterance !== 'undefined';
    const synth = supportsSpeech ? window.speechSynthesis : null;

    let activeRoot = null;
    let activeChunks = [];
    let activeIndex = 0;
    let activeToken = 0;
    let activeState = 'idle';
    let pendingWait = null;

    function readyMessage(root) {
        return root.getAttribute('data-read-aloud-ready') || 'Use your browser voice to listen to this lesson.';
    }

    function emptyMessage(root) {
        return root.getAttribute('data-read-aloud-empty') || 'No readable text is available yet.';
    }

    function statusNode(root) {
        return root.querySelector('[data-read-aloud-status]');
    }

    function setStatus(root, message) {
        const node = statusNode(root);
        if (node) {
            node.textContent = message;
        }
    }

    function button(root, name) {
        return root.querySelector('[data-read-aloud-' + name + ']');
    }

    function voiceSelect(root) {
        return root.querySelector('[data-read-aloud-voice]');
    }

    function rateControl(root) {
        return root.querySelector('[data-read-aloud-rate]');
    }

    function updateButtons(root, state) {
        const play = button(root, 'play');
        const pause = button(root, 'pause');
        const resume = button(root, 'resume');
        const stop = button(root, 'stop');
        const isSupported = supportsSpeech;

        if (play) {
            play.disabled = !isSupported || state === 'loading' || state === 'speaking' || state === 'paused';
        }
        if (pause) {
            pause.disabled = !isSupported || state !== 'speaking';
        }
        if (resume) {
            resume.disabled = !isSupported || state !== 'paused';
        }
        if (stop) {
            stop.disabled = !isSupported || (state !== 'loading' && state !== 'speaking' && state !== 'paused');
        }
    }

    function invalidateWait() {
        if (!pendingWait) {
            return;
        }

        pendingWait.observer.disconnect();
        window.clearTimeout(pendingWait.timeoutId);
        pendingWait = null;
    }

    function invalidatePlayback() {
        activeToken += 1;
        invalidateWait();

        if (synth && (synth.speaking || synth.paused)) {
            synth.cancel();
        }

        activeChunks = [];
        activeIndex = 0;
        activeState = 'idle';
    }

    function stopActive(message) {
        const previousRoot = activeRoot;
        invalidatePlayback();
        activeRoot = null;

        if (previousRoot) {
            setStatus(previousRoot, message || readyMessage(previousRoot));
            updateButtons(previousRoot, 'idle');
        }
    }

    function normalizeText(text) {
        return (text || '')
            .replace(/\u00a0/g, ' ')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function selectorList(root) {
        return (root.getAttribute('data-read-aloud-selectors') || '')
            .split(',')
            .map((selector) => selector.trim())
            .filter(Boolean);
    }

    function collectText(root) {
        const doc = root.ownerDocument;
        const selectors = selectorList(root);
        const fragments = [];
        const seen = new Set();

        selectors.forEach(function (selector) {
            let nodes = [];

            try {
                nodes = Array.from(doc.querySelectorAll(selector));
            } catch (error) {
                nodes = [];
            }

            nodes.forEach(function (node) {
                const text = normalizeText(node.innerText || node.textContent || '');
                if (text !== '' && !seen.has(text)) {
                    seen.add(text);
                    fragments.push(text);
                }
            });
        });

        return normalizeText(fragments.join(' '));
    }

    function splitIntoChunks(text) {
        const limit = 220;
        const sentences = text.match(/[^.!?]+[.!?]?/g) || [text];
        const chunks = [];
        let current = '';

        sentences.forEach(function (sentence) {
            let piece = normalizeText(sentence);
            if (piece === '') {
                return;
            }

            if (piece.length > limit) {
                if (current !== '') {
                    chunks.push(current);
                    current = '';
                }

                while (piece.length > limit) {
                    const slice = piece.slice(0, limit);
                    let breakAt = Math.max(slice.lastIndexOf(', '), slice.lastIndexOf(' '));
                    if (breakAt < Math.floor(limit * 0.5)) {
                        breakAt = limit;
                    }

                    chunks.push(normalizeText(piece.slice(0, breakAt)));
                    piece = normalizeText(piece.slice(breakAt));
                }

                current = piece;
                return;
            }

            const candidate = current === '' ? piece : current + ' ' + piece;
            if (candidate.length > limit) {
                chunks.push(current);
                current = piece;
                return;
            }

            current = candidate;
        });

        if (current !== '') {
            chunks.push(current);
        }

        return chunks.length ? chunks : [text];
    }

    function selectVoice(root) {
        if (!synth) {
            return null;
        }

        const voices = synth.getVoices();
        const select = voiceSelect(root);
        const selectedUri = select ? select.value : '';
        if (selectedUri !== '') {
            const matchingVoice = voices.find(function (voice) {
                return voice.voiceURI === selectedUri;
            });

            if (matchingVoice) {
                return matchingVoice;
            }
        }

        if (!voices.length) {
            return null;
        }

        const requestedLang = (
            root.getAttribute('data-read-aloud-lang') ||
            document.documentElement.lang ||
            navigator.language ||
            ''
        ).toLowerCase();
        const primaryLang = requestedLang.split('-')[0];

        return voices.find(function (voice) {
            return requestedLang !== '' && voice.lang && voice.lang.toLowerCase() === requestedLang;
        }) || voices.find(function (voice) {
            return primaryLang !== '' && voice.lang && voice.lang.toLowerCase().startsWith(primaryLang);
        }) || voices[0];
    }

    function selectedRate(root) {
        const control = rateControl(root);
        const rawValue = control ? control.value : (root.getAttribute('data-read-aloud-rate') || '1');
        const parsedValue = Number.parseFloat(rawValue);

        return Number.isFinite(parsedValue) ? Math.min(1.4, Math.max(0.7, parsedValue)) : 1;
    }

    function populateVoiceOptions(root) {
        const select = voiceSelect(root);
        if (!select) {
            return;
        }

        const previousValue = select.value;
        const voices = synth ? synth.getVoices() : [];

        select.innerHTML = '';

        const autoOption = document.createElement('option');
        autoOption.value = '';
        autoOption.textContent = 'Browser Default';
        select.appendChild(autoOption);

        voices.forEach(function (voice) {
            const option = document.createElement('option');
            option.value = voice.voiceURI;
            option.textContent = voice.name + ' (' + voice.lang + ')';
            select.appendChild(option);
        });

        if (previousValue !== '' && voices.some(function (voice) { return voice.voiceURI === previousValue; })) {
            select.value = previousValue;
            return;
        }

        select.value = '';
    }

    function speakNextChunk(token) {
        if (!synth || token !== activeToken || !activeRoot) {
            return;
        }

        if (activeIndex >= activeChunks.length) {
            const completedRoot = activeRoot;
            activeRoot = null;
            activeChunks = [];
            activeIndex = 0;
            activeState = 'idle';
            setStatus(completedRoot, 'Read aloud finished.');
            updateButtons(completedRoot, 'idle');
            return;
        }

        const utterance = new SpeechSynthesisUtterance(activeChunks[activeIndex]);
        const voice = selectVoice(activeRoot);
        const rate = selectedRate(activeRoot);
        const progressMessage = activeChunks.length > 1
            ? 'Reading aloud ' + (activeIndex + 1) + ' of ' + activeChunks.length + '...'
            : 'Reading aloud...';

        if (voice) {
            utterance.voice = voice;
        }

        setStatus(activeRoot, progressMessage);
        utterance.rate = rate;
        utterance.onend = function () {
            if (token !== activeToken) {
                return;
            }

            activeIndex += 1;
            speakNextChunk(token);
        };
        utterance.onerror = function () {
            if (token !== activeToken) {
                return;
            }

            const failedRoot = activeRoot;
            activeRoot = null;
            activeChunks = [];
            activeIndex = 0;
            activeState = 'idle';

            if (failedRoot) {
                setStatus(failedRoot, 'Read aloud stopped because the browser voice service failed.');
                updateButtons(failedRoot, 'idle');
            }
        };

        synth.speak(utterance);
    }

    function startPlayback(root, text, token) {
        activeChunks = splitIntoChunks(text);
        activeIndex = 0;
        activeState = 'speaking';
        updateButtons(root, 'speaking');
        speakNextChunk(token);
    }

    function waitForReadableText(root, token) {
        return new Promise(function (resolve, reject) {
            const doc = root.ownerDocument;

            function cleanup() {
                if (pendingWait) {
                    pendingWait.observer.disconnect();
                    window.clearTimeout(pendingWait.timeoutId);
                    pendingWait = null;
                }
            }

            function tryResolve() {
                if (token !== activeToken || activeRoot !== root) {
                    cleanup();
                    reject(new Error('cancelled'));
                    return;
                }

                const text = collectText(root);
                if (text !== '') {
                    cleanup();
                    resolve(text);
                }
            }

            const existingText = collectText(root);
            if (existingText !== '') {
                resolve(existingText);
                return;
            }

            const observer = new MutationObserver(tryResolve);
            observer.observe(doc.body || doc.documentElement, {
                subtree: true,
                childList: true,
                characterData: true,
            });

            const timeoutId = window.setTimeout(function () {
                cleanup();
                reject(new Error('timeout'));
            }, 12000);

            pendingWait = {
                observer: observer,
                timeoutId: timeoutId,
            };
        });
    }

    function handlePlay(root) {
        if (!supportsSpeech) {
            setStatus(root, 'Read aloud is not supported in this browser.');
            updateButtons(root, 'idle');
            return;
        }

        if (activeRoot === root && activeState === 'paused' && synth) {
            synth.resume();
            activeState = 'speaking';
            setStatus(root, 'Read aloud resumed.');
            updateButtons(root, 'speaking');
            return;
        }

        stopActive();
        activeRoot = root;
        activeToken += 1;
        const token = activeToken;

        setStatus(root, 'Preparing read aloud...');
        updateButtons(root, 'loading');

        waitForReadableText(root, token)
            .then(function (text) {
                if (token !== activeToken || activeRoot !== root) {
                    return;
                }

                startPlayback(root, text, token);
            })
            .catch(function (error) {
                if (error && error.message === 'cancelled') {
                    return;
                }

                if (token !== activeToken || activeRoot !== root) {
                    return;
                }

                activeRoot = null;
                activeChunks = [];
                activeIndex = 0;
                activeState = 'idle';
                setStatus(root, emptyMessage(root));
                updateButtons(root, 'idle');
            });
    }

    function handlePause(root) {
        if (!synth || activeRoot !== root || activeState !== 'speaking') {
            return;
        }

        synth.pause();
        activeState = 'paused';
        setStatus(root, 'Read aloud paused.');
        updateButtons(root, 'paused');
    }

    function handleResume(root) {
        if (!synth || activeRoot !== root || activeState !== 'paused') {
            return;
        }

        synth.resume();
        activeState = 'speaking';
        setStatus(root, 'Read aloud resumed.');
        updateButtons(root, 'speaking');
    }

    function handleStop(root) {
        if (activeRoot !== root) {
            setStatus(root, readyMessage(root));
            updateButtons(root, 'idle');
            return;
        }

        stopActive('Read aloud stopped.');
    }

    function bind(root) {
        const play = button(root, 'play');
        const pause = button(root, 'pause');
        const resume = button(root, 'resume');
        const stop = button(root, 'stop');

        if (play) {
            play.addEventListener('click', function () {
                handlePlay(root);
            });
        }
        if (pause) {
            pause.addEventListener('click', function () {
                handlePause(root);
            });
        }
        if (resume) {
            resume.addEventListener('click', function () {
                handleResume(root);
            });
        }
        if (stop) {
            stop.addEventListener('click', function () {
                handleStop(root);
            });
        }

        populateVoiceOptions(root);
        setStatus(root, supportsSpeech ? readyMessage(root) : 'Read aloud is not supported in this browser.');
        updateButtons(root, 'idle');
    }

    roots.forEach(bind);

    if (supportsSpeech) {
        roots.forEach(populateVoiceOptions);
        if (typeof synth.addEventListener === 'function') {
            synth.addEventListener('voiceschanged', function () {
                roots.forEach(populateVoiceOptions);
            });
        } else {
            synth.onvoiceschanged = function () {
                roots.forEach(populateVoiceOptions);
            };
        }
        window.addEventListener('pagehide', function () {
            stopActive();
        });
    }
})();
