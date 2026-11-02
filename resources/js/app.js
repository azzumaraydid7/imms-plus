//
// IMMS control deck — window chrome wiring.
//
// NativePHP's renderer bridge (window.Native) only exposes `on()` and
// `contextMenu()`, so there is no JS API for minimise / maximise / close.
// Those are executed server-side through the NativePHP Window facade and
// triggered from here over fetch().
//

function csrfToken() {
    var meta = document.querySelector('meta[name="csrf-token"]');

    return meta ? meta.getAttribute('content') : '';
}

var windowControls = document.querySelectorAll('[data-window-action]');

Array.prototype.forEach.call(windowControls, function (button) {
    button.addEventListener('click', function () {
        var endpoint = button.getAttribute('data-endpoint');

        if (! endpoint) {
            return;
        }

        button.disabled = true;

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken(),
                Accept: 'application/json',
            },
            credentials: 'same-origin',
        })
            .catch(function () {
                // The window is likely already closing — nothing useful to report.
            })
            .then(function () {
                button.disabled = false;
            });
    });
});

// Live clock, shared by the title bar and the status bar.
var clock = document.querySelector('[data-clock]');

if (clock) {
    var renderClock = function () {
        clock.textContent = new Date().toLocaleTimeString('en-GB', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
        });
    };

    renderClock();

    window.setInterval(renderClock, 1000);
}
