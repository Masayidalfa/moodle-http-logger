/* eslint-disable no-console */
define([], function() {
    return {
        init: function() {
            let fieldsetData = {};

            /**
             * Menambahkan event listener ke elemen form untuk menangkap perubahan nilai input.
             * @param {HTMLFormElement} form 
             */
            function attachFieldsetListeners(form) {
                let fieldsetElements = form.querySelectorAll('fieldset input, fieldset select, fieldset textarea');
                fieldsetElements.forEach(element => {
                    if (!element.hasAttribute('data-listener-attached')) {
                        element.addEventListener('input', function() {
                            if (element.name) {
                                fieldsetData[element.name] = element.value;
                            }
                        });
                        element.setAttribute('data-listener-attached', 'true');
                    }
                });
            }

            /**
             * Mengumpulkan dan mengirim data form ke server melalui fetch atau Beacon API.
             * @param {HTMLFormElement} form 
             */
            function logFormData(form) {
                if (!form || form.dataset.loggedOnce) return;

                form.dataset.loggedOnce = new Date().getTime();
                setTimeout(() => delete form.dataset.loggedOnce, 1000);

                attachFieldsetListeners(form);

                let formData = new FormData(form);
                for (let key in fieldsetData) {
                    if (fieldsetData.hasOwnProperty(key) && !formData.has(key)) {
                        formData.append(key, fieldsetData[key]);
                    }
                }

                formData.append('submitted_at', new Date().toISOString());
                let url = M.cfg.wwwroot + '/local/requestlogger/log.php';

                if (navigator.sendBeacon) {
                    let blob = new Blob([new URLSearchParams(formData)], {
                        type: 'application/x-www-form-urlencoded'
                    });
                    navigator.sendBeacon(url, blob);
                } else {
                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });
                }
            }

            // Menangani semua event form submit dengan metode POST
            document.addEventListener('submit', function(event) {
                let form = event.target;
                if (!form.method || form.method.toLowerCase() !== 'post') return;
                logFormData(form);
            }, true);

            // Menangani form dalam modal kalender (khusus untuk fitur event Moodle)
            document.addEventListener('click', function(event) {
                let target = event.target.closest('button[data-action="save"]');
                if (!target) return;

                let modal = document.querySelector('.modal.show');
                if (!modal) return;

                let form = modal.querySelector('.mform');
                if (!form) {
                    let waitForForm = setInterval(() => {
                        let newModal = document.querySelector('.modal.show');
                        if (newModal) {
                            let newForm = newModal.querySelector('.mform');
                            if (newForm) {
                                clearInterval(waitForForm);
                                logFormData(newForm);
                            }
                        } else {
                            clearInterval(waitForForm);
                        }
                    }, 300);

                    setTimeout(() => clearInterval(waitForForm), 3000);
                } else {
                    logFormData(form);
                }
            }, true);

            /**
             * Mengamati perubahan DOM untuk mendeteksi kemunculan modal kalender
             * dan menambahkan listener ke form yang muncul.
             */
            function observeModal() {
                let observer = new MutationObserver(mutationsList => {
                    mutationsList.forEach(mutation => {
                        mutation.addedNodes.forEach(node => {
                            if (node.nodeType === 1 && node.matches('.modal.show .mform')) {
                                attachFieldsetListeners(node);
                            }
                        });

                        mutation.removedNodes.forEach(node => {
                            if (node.nodeType === 1 && node.matches('.modal.show')) {
                                observer.disconnect();
                            }
                        });
                    });
                });

                observer.observe(document.body, { childList: true, subtree: true });
            }

            // Memulai observer saat tombol "New Event" ditekan
            document.addEventListener('click', function(event) {
                let newEventButton = event.target.closest('button[data-action="new-event-button"]');
                if (!newEventButton) return;
                observeModal();
            });

            /**
             * Override fungsi fetch (opsional) hanya untuk tujuan debugging di awal pengembangan.
             * Baris ini dibiarkan nonaktif, dan dapat dihapus di produksi.
             */
            // (function() {
            //     let originalFetch = window.fetch;
            //     window.fetch = function(url, options) {
            //         return originalFetch.apply(this, arguments);
            //     };
            // })();
        }
    };
});
