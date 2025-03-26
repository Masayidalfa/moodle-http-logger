/* eslint-disable no-console */
define([], function() {
    return {
        init: function() {
            console.log("Form logger initialized.");

            let fieldsetData = {};

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
                    let blob = new Blob([new URLSearchParams(formData)], { type: 'application/x-www-form-urlencoded' });
                    navigator.sendBeacon(url, blob);
                } else {
                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    }).then(response => response.json())
                      .then(data => console.log('✅ Log terkirim:', data))
                      .catch(error => console.error('❌ Gagal mengirim log:', error));
                }
            }

            document.addEventListener('submit', function(event) {
                let form = event.target;
                if (!form.method || form.method.toLowerCase() !== 'post') return;
                logFormData(form);
            }, true);

            // 🔥 MENANGKAP FORM DI MODAL KALENDER DENGAN EVENT LISTENER LANGSUNG 🔥
            document.addEventListener('click', function(event) {
                let target = event.target.closest('button[data-action="save"]');
                if (!target) return;

                console.log("🟢 Tombol Save kalender ditekan:", target);

                let modal = document.querySelector('.modal.show');
                if (!modal) {
                    console.error("❌ Tidak menemukan modal yang terbuka.");
                    return;
                }

                let form = modal.querySelector('.mform');
                if (!form) {
                    console.error("❌ Form tidak ditemukan dalam modal, menunggu beberapa detik...");
                    
                    let waitForForm = setInterval(() => {
                        let newModal = document.querySelector('.modal.show');
                        if (newModal) {
                            let newForm = newModal.querySelector('.mform');
                            if (newForm) {
                                console.log("✅ Form ditemukan dalam modal setelah penundaan.");
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

            // 🔍 OBSERVER MODAL KALENDER TETAP AKTIF HINGGA MODAL TERTUTUP
            function observeModal() {
                console.log("🔍 Memulai observer modal kalender...");
                let observer = new MutationObserver(mutationsList => {
                    mutationsList.forEach(mutation => {
                        mutation.addedNodes.forEach(node => {
                            if (node.nodeType === 1 && node.matches('.modal.show .mform')) {
                                console.log("✅ Form kalender muncul dalam modal, menambahkan listener...");
                                attachFieldsetListeners(node);
                            }
                        });

                        mutation.removedNodes.forEach(node => {
                            if (node.nodeType === 1 && node.matches('.modal.show')) {
                                console.log("🛑 Modal ditutup, menghentikan observer.");
                                observer.disconnect();
                            }
                        });
                    });
                });

                observer.observe(document.body, { childList: true, subtree: true });
            }

            document.addEventListener('click', function(event) {
                let newEventButton = event.target.closest('button[data-action="new-event-button"]');
                if (!newEventButton) return;

                console.log("📌 Tombol 'New Event' ditekan, memantau modal...");
                observeModal();
            });

            // 🛠 MENCEGAH FETCH LOGGER BERLEBIHAN
            (function() {
                let originalFetch = window.fetch;
                window.fetch = function(url, options) {
                    console.log("📡 AJAX POST ke:", url);
                    return originalFetch.apply(this, arguments);
                };
            })();
        }
    };
});
