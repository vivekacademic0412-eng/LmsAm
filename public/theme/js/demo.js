function toggleBitmoji() {
    const msg = document.getElementById('bitmojiMsg');
    msg.style.display = msg.style.display === 'none' ? 'block' : 'none';
}
// Auto-hide bubble after 6s, re-show on hover
setTimeout(() => {
    const msg = document.getElementById('bitmojiMsg');
    if (msg) msg.style.display = 'none';
}, 6000);
// document.querySelector('.bitmoji-avatar').addEventListener('mouseenter', () => {
//     document.getElementById('bitmojiMsg').style.display = 'block';
// });
// Add inside layout.blade.php before </body>
document.addEventListener('DOMContentLoaded', function(){

    const avatar =
        document.querySelector('.bitmoji-avatar');

    if(avatar){

        avatar.addEventListener('mouseenter', function(){

            console.log('hover');

        });

    }

});
function toggleBitmoji() {
    const msg = document.getElementById('bitmojiMsg');
    msg.style.display = msg.style.display === 'none' ? 'block' : 'none';
}

// Auto-hide after 7s, re-show on hover
setTimeout(() => {
    const msg = document.getElementById('bitmojiMsg');
    if (msg) msg.style.display = 'none';
}, 7000);

const avatar = document.querySelector('.bitmoji-avatar');
if (avatar) {
    avatar.addEventListener('mouseenter', () => {
        document.getElementById('bitmojiMsg').style.display = 'block';
    });
}
const bmMsgs = [
    "Hi! I'm Acedmic Mantra👋 Let's start!",
    "You're going to love this! 😊",
    "I'll guide every step!",
    "Fill in your details below",
    "Let's gooo! "
];
let bmIdx = 0;

function waveHello() {
    const arm = document.getElementById('waveArmG');
    const sp = document.getElementById('bmSpeech');
    if (!arm || !sp) return;
    arm.style.animation = 'none';
    void arm.offsetWidth;
    arm.style.animation = 'waveArm .5s ease-in-out 3';
    bmIdx = (bmIdx + 1) % bmMsgs.length;
    sp.textContent = bmMsgs[bmIdx];
    sp.style.animation = 'none';
    void sp.offsetWidth;
    sp.style.animation = 'bmBubble .4s ease both';
}
setInterval(() => {
    const sp = document.getElementById('bmSpeech');
    if (!sp) return;
    bmIdx = (bmIdx + 1) % bmMsgs.length;
    sp.textContent = bmMsgs[bmIdx];
    sp.style.animation = 'none';
    void sp.offsetWidth;
    sp.style.animation = 'bmBubble .4s ease both';
}, 4500);

const themeBtn =
    document.getElementById("themeBtn");

const saved =
    localStorage.getItem("theme");

if (saved) {

    document.documentElement
        .setAttribute(
            "data-theme",
            saved
        );

    themeBtn.innerHTML =
        saved === "dark" ?
        "☀️" :
        "🌙";

}


themeBtn.onclick = () => {

    const current =
        document.documentElement
        .getAttribute("data-theme");

    const next =
        current === "dark" ?
        "light" :
        "dark";

    document.documentElement
        .setAttribute(
            "data-theme",
            next
        );

    localStorage
        .setItem(
            "theme",
            next
        );

    themeBtn.innerHTML =
        next === "dark" ?
        "☀️" :
        "🌙";

};
