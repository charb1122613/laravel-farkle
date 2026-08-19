document.addEventListener('DOMContentLoaded', () => {
    const mediaQuery = window.matchMedia('(max-width: 1400px)');

    const sidebar = document.querySelector('.rules-wrapper');
    const btnOpenMenu = document.getElementById('btn-open-menu');
    const btnCloseMenu = document.getElementById('btn-close-menu');

    const btnOpenHowTo = document.querySelectorAll('.btn-open-how-to');
    const btnCloseHowTo = document.getElementById('btn-close-how-to');
    const containerHowTo = document.getElementById('how-to-container'); 

    function handleBreakpointChange(e) {
        if (e.matches) {
            sidebar.classList.add('hidden');
        } else {
            sidebar.classList.remove('hidden');
        }
    }

    handleBreakpointChange(mediaQuery);

    mediaQuery.addEventListener('change', handleBreakpointChange);

    btnOpenMenu.addEventListener('click', () => {
        sidebar.classList.remove('hidden');
    });

    btnCloseMenu.addEventListener('click', () => {
        sidebar.classList.add('hidden');
    });


    btnOpenHowTo.forEach(btn => {
        btn.addEventListener("click", () => {
            containerHowTo.classList.remove('hidden');
            handleBreakpointChange(mediaQuery);
        })
    });

    btnCloseHowTo.addEventListener("click", () => {
        containerHowTo.classList.add('hidden');
    });
});