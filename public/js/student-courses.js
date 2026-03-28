(function () {
    function initCategoryTabs() {
        var tabRoot = document.getElementById('categoryTabs');
        var tabs = document.querySelectorAll('#categoryTabs .tab-btn');
        var panels = document.querySelectorAll('[data-tab-panel]');
        if (!tabRoot || !tabs.length) return;

        function resetSubtabs(panel) {
            var subBtns = panel.querySelectorAll('[data-subtab]');
            var items = panel.querySelectorAll('[data-subcat]');
            subBtns.forEach(function (btn) { btn.classList.toggle('active', btn.getAttribute('data-subtab') === 'all'); });
            items.forEach(function (item) { item.style.display = ''; });
        }

        function activateTab(id) {
            tabs.forEach(function (b) { b.classList.toggle('active', b.getAttribute('data-tab') === id); });
            panels.forEach(function (panel) {
                var isActive = panel.getAttribute('data-tab-panel') === id;
                panel.classList.toggle('active', isActive);
                if (isActive) {
                    resetSubtabs(panel);
                }
            });
        }

        tabRoot.addEventListener('click', function (event) {
            var btn = event.target.closest('.tab-btn');
            if (!btn) return;
            activateTab(btn.getAttribute('data-tab'));
        });

        document.querySelectorAll('[data-subtabs]').forEach(function (row) {
            row.addEventListener('click', function (event) {
                var btn = event.target.closest('.subtab-btn');
                if (!btn) return;
                event.stopPropagation();
                var target = btn.getAttribute('data-subtab');
                var panel = row.closest('.tab-panel');
                var subBtns = row.querySelectorAll('.subtab-btn');
                var items = panel.querySelectorAll('[data-subcat]');
                subBtns.forEach(function (b) { b.classList.toggle('active', b === btn); });
                items.forEach(function (item) {
                    var subcat = item.getAttribute('data-subcat');
                    item.style.display = (target === 'all' || subcat === target) ? '' : 'none';
                });
            });
        });

        var initial = tabRoot.querySelector('.tab-btn.active') || tabs[0];
        if (initial) {
            activateTab(initial.getAttribute('data-tab'));
        }
    }

    function initDemoVideoSliders() {
        document.querySelectorAll('[data-demo-video-slider]').forEach(function (root) {
            var track = root.querySelector('[data-demo-video-track]');
            var slides = root.querySelectorAll('[data-demo-video-slide]');
            if (!track || slides.length <= 1) return;

            var prev = root.querySelector('[data-demo-video-prev]');
            var next = root.querySelector('[data-demo-video-next]');
            var counter = root.querySelector('[data-demo-video-counter]');
            var dots = root.querySelectorAll('[data-demo-video-dot]');
            var current = 0;

            function syncSlider() {
                track.style.transform = 'translateX(-' + (current * 100) + '%)';

                slides.forEach(function (slide, index) {
                    var isActive = index === current;
                    slide.classList.toggle('active', isActive);
                    slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');

                    var video = slide.querySelector('video');
                    if (video && !isActive) {
                        video.pause();
                    }

                    var iframe = slide.querySelector('[data-demo-youtube-embed]');
                    if (iframe && !isActive && iframe.contentWindow) {
                        iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
                    }
                });

                dots.forEach(function (dot, index) {
                    var isActive = index === current;
                    dot.classList.toggle('active', isActive);
                    dot.setAttribute('aria-current', isActive ? 'true' : 'false');
                });

                if (counter) {
                    counter.textContent = (current + 1) + ' / ' + slides.length;
                }
            }

            function goToSlide(index) {
                current = (index + slides.length) % slides.length;
                syncSlider();
            }

            if (prev) {
                prev.addEventListener('click', function () {
                    goToSlide(current - 1);
                });
            }

            if (next) {
                next.addEventListener('click', function () {
                    goToSlide(current + 1);
                });
            }

            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    var targetIndex = parseInt(dot.getAttribute('data-demo-video-dot'), 10);
                    goToSlide(isNaN(targetIndex) ? 0 : targetIndex);
                });
            });

            syncSlider();
        });
    }

    function initCourseDetailAccordions() {
        var root = document.querySelector('[data-course-detail]');
        if (!root) return;

        var weekStages = Array.prototype.slice.call(root.querySelectorAll('[data-week-stage]'));
        var weekPanels = Array.prototype.slice.call(root.querySelectorAll('[data-week-panel]'));
        var weekToggles = Array.prototype.slice.call(root.querySelectorAll('[data-week-toggle]'));
        var sessionStages = Array.prototype.slice.call(root.querySelectorAll('[data-session-stage]'));
        var sessionPanels = Array.prototype.slice.call(root.querySelectorAll('[data-session-panel]'));
        var sessionToggles = Array.prototype.slice.call(root.querySelectorAll('[data-session-toggle]'));
        var itemLinks = Array.prototype.slice.call(root.querySelectorAll('[data-item-link]'));
        var viewerHosts = Array.prototype.slice.call(root.querySelectorAll('[data-item-viewer-host]'));
        var activeWeekId = root.getAttribute('data-selected-week') || '';
        var activeSessionId = root.getAttribute('data-selected-session') || '';
        var activeItemId = root.getAttribute('data-selected-item') || '';

        function isPlainLeftClick(event) {
            return event.button === 0 && !event.metaKey && !event.ctrlKey && !event.shiftKey && !event.altKey;
        }

        function updateToggle(toggle, open) {
            if (!toggle) return;
            toggle.classList.toggle('course-action--soft', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            var label = toggle.querySelector('[data-toggle-label]');
            if (label) {
                label.textContent = open
                    ? (toggle.getAttribute('data-close-label') || 'Close')
                    : (toggle.getAttribute('data-open-label') || 'Open');
            }
        }

        function updateItemLink(link, open) {
            if (!link) return;
            link.classList.toggle('item-nav--active', open);
            link.setAttribute('aria-expanded', open ? 'true' : 'false');
            var label = link.querySelector('[data-item-action-label]');
            if (label) {
                label.textContent = open ? 'Hide Item' : 'Open Item';
            }
        }

        function renderTemplateIntoHost(host, templateId) {
            if (!host) return;
            if (host.getAttribute('data-active-template') === templateId) return;

            var template = document.getElementById(templateId);
            if (!template) return;

            host.innerHTML = '';
            host.appendChild(template.content.cloneNode(true));
            host.setAttribute('data-active-template', templateId);
            refreshOpenHeights();

            host.querySelectorAll('video, iframe').forEach(function (media) {
                media.addEventListener('loadedmetadata', refreshOpenHeights);
                media.addEventListener('load', refreshOpenHeights);
            });
        }

        function setPanelState(panel, open, immediate) {
            if (!panel) return;

            if (open) {
                panel.hidden = false;
                panel.style.overflow = 'hidden';
                panel.classList.add('is-open');

                if (immediate) {
                    panel.style.maxHeight = 'none';
                    panel.style.overflow = 'visible';
                } else {
                    panel.style.maxHeight = '0px';

                    function onOpenEnd(event) {
                        if (event.target !== panel || event.propertyName !== 'max-height') return;
                        if (panel.classList.contains('is-open')) {
                            panel.style.maxHeight = 'none';
                            panel.style.overflow = 'visible';
                        }
                        panel.removeEventListener('transitionend', onOpenEnd);
                    }

                    panel.addEventListener('transitionend', onOpenEnd);
                    requestAnimationFrame(function () {
                        panel.style.maxHeight = panel.scrollHeight + 'px';
                    });
                }
                return;
            }

            panel.hidden = false;
            panel.style.overflow = 'hidden';

            if (panel.style.maxHeight === 'none' || !panel.style.maxHeight) {
                panel.style.maxHeight = panel.scrollHeight + 'px';
            }

            if (immediate) {
                panel.classList.remove('is-open');
                panel.style.maxHeight = '0px';
                panel.hidden = true;
                return;
            }

            requestAnimationFrame(function () {
                panel.classList.remove('is-open');
                panel.style.maxHeight = '0px';
            });

            function onTransitionEnd(event) {
                if (event.target !== panel || event.propertyName !== 'max-height') return;
                if (!panel.classList.contains('is-open')) {
                    panel.hidden = true;
                }
                panel.removeEventListener('transitionend', onTransitionEnd);
            }

            panel.addEventListener('transitionend', onTransitionEnd);
        }

        function syncWeeks(immediate) {
            weekStages.forEach(function (stage) {
                var isOpen = activeWeekId !== '' && stage.getAttribute('data-week-stage') === activeWeekId;
                stage.classList.toggle('week-stage--active', isOpen);
            });

            weekPanels.forEach(function (panel) {
                var isOpen = activeWeekId !== '' && panel.getAttribute('data-week-panel') === activeWeekId;
                setPanelState(panel, isOpen, immediate);
            });

            weekToggles.forEach(function (toggle) {
                var isOpen = activeWeekId !== '' && toggle.getAttribute('data-week-toggle') === activeWeekId;
                updateToggle(toggle, isOpen);
            });
        }

        function syncSessions(immediate) {
            sessionStages.forEach(function (stage) {
                var parentWeek = stage.closest('[data-week-stage]');
                var isOpen = activeSessionId !== ''
                    && stage.getAttribute('data-session-stage') === activeSessionId
                    && parentWeek
                    && parentWeek.getAttribute('data-week-stage') === activeWeekId;
                stage.classList.toggle('session-stage--active', isOpen);
            });

            sessionPanels.forEach(function (panel) {
                var parentWeek = panel.closest('[data-week-stage]');
                var isOpen = activeSessionId !== ''
                    && panel.getAttribute('data-session-panel') === activeSessionId
                    && parentWeek
                    && parentWeek.getAttribute('data-week-stage') === activeWeekId;
                setPanelState(panel, isOpen, immediate);
            });

            sessionToggles.forEach(function (toggle) {
                var isOpen = activeSessionId !== ''
                    && toggle.getAttribute('data-session-toggle') === activeSessionId
                    && toggle.getAttribute('data-parent-week') === activeWeekId;
                updateToggle(toggle, isOpen);
            });
        }

        function updateUrl() {
            var url = new URL(window.location.href);

            if (activeWeekId) {
                url.searchParams.set('week', activeWeekId);
            } else {
                url.searchParams.delete('week');
                url.searchParams.delete('session');
                url.searchParams.delete('item');
            }

            if (activeSessionId) {
                url.searchParams.set('session', activeSessionId);
            } else {
                url.searchParams.delete('session');
                url.searchParams.delete('item');
            }

            if (activeItemId && activeSessionId) {
                url.searchParams.set('item', activeItemId);
            } else {
                url.searchParams.delete('item');
            }

            history.replaceState({}, '', url.pathname + (url.search ? url.search : '') + url.hash);
        }

        function syncItems() {
            itemLinks.forEach(function (link) {
                var isOpen = activeItemId !== ''
                    && link.getAttribute('data-item-link') === activeItemId
                    && link.getAttribute('data-session-id') === activeSessionId
                    && link.getAttribute('data-week-id') === activeWeekId;
                updateItemLink(link, isOpen);
            });
        }

        function syncViewerHosts() {
            viewerHosts.forEach(function (host) {
                var sessionId = host.getAttribute('data-item-viewer-host');
                var placeholderTemplate = host.getAttribute('data-placeholder-template');
                var templateId = placeholderTemplate;

                if (activeItemId !== '' && sessionId === activeSessionId) {
                    var link = root.querySelector('[data-item-link="' + activeItemId + '"][data-session-id="' + sessionId + '"]');
                    if (link) {
                        templateId = link.getAttribute('data-item-template') || placeholderTemplate;
                    }
                }

                renderTemplateIntoHost(host, templateId);
            });
        }

        function refreshOpenHeights() {
            weekPanels.forEach(function (panel) {
                if (panel.classList.contains('is-open')) {
                    panel.style.maxHeight = 'none';
                    panel.style.overflow = 'visible';
                }
            });

            sessionPanels.forEach(function (panel) {
                if (panel.classList.contains('is-open')) {
                    panel.style.maxHeight = 'none';
                    panel.style.overflow = 'visible';
                }
            });
        }

        weekToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function (event) {
                if (!isPlainLeftClick(event)) return;
                event.preventDefault();

                var targetWeekId = toggle.getAttribute('data-week-toggle');
                var isAlreadyOpen = activeWeekId === targetWeekId;

                activeWeekId = isAlreadyOpen ? '' : targetWeekId;
                activeSessionId = '';
                activeItemId = '';
                syncWeeks(false);
                syncSessions(false);
                syncItems();
                syncViewerHosts();
                updateUrl();

                var targetStage = root.querySelector('[data-week-stage="' + targetWeekId + '"]');
                if (!isAlreadyOpen && targetStage) {
                    targetStage.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        sessionToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function (event) {
                if (!isPlainLeftClick(event)) return;
                event.preventDefault();

                var targetSessionId = toggle.getAttribute('data-session-toggle');
                var targetWeekId = toggle.getAttribute('data-parent-week');
                var isAlreadyOpen = activeSessionId === targetSessionId && activeWeekId === targetWeekId;

                activeWeekId = targetWeekId || activeWeekId;
                activeSessionId = isAlreadyOpen ? '' : targetSessionId;
                activeItemId = '';

                syncWeeks(false);
                syncSessions(false);
                syncItems();
                syncViewerHosts();
                updateUrl();

                var targetStage = root.querySelector('[data-session-stage="' + targetSessionId + '"]');
                if (!isAlreadyOpen && targetStage) {
                    targetStage.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        itemLinks.forEach(function (link) {
            link.addEventListener('click', function (event) {
                if (!isPlainLeftClick(event)) return;
                event.preventDefault();

                var targetWeekId = link.getAttribute('data-week-id') || '';
                var targetSessionId = link.getAttribute('data-session-id') || '';
                var targetItemId = link.getAttribute('data-item-link') || '';
                var isAlreadyOpen = activeWeekId === targetWeekId
                    && activeSessionId === targetSessionId
                    && activeItemId === targetItemId;

                activeWeekId = targetWeekId;
                activeSessionId = targetSessionId;
                activeItemId = isAlreadyOpen ? '' : targetItemId;

                syncWeeks(false);
                syncSessions(false);
                syncItems();
                syncViewerHosts();
                updateUrl();

                var targetHost = root.querySelector('[data-item-viewer-host="' + targetSessionId + '"]');
                if (targetHost) {
                    targetHost.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        syncWeeks(true);
        syncSessions(true);
        syncItems();
        syncViewerHosts();
        root.querySelectorAll('video, iframe').forEach(function (media) {
            media.addEventListener('loadedmetadata', refreshOpenHeights);
            media.addEventListener('load', refreshOpenHeights);
        });
        window.addEventListener('load', refreshOpenHeights);
        window.addEventListener('resize', refreshOpenHeights);
    }

    initCategoryTabs();
    initDemoVideoSliders();
    initCourseDetailAccordions();
})();
