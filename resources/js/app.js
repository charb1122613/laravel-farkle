const btnOpenHowTo = document.getElementById('btn-open-how-to');
const btnCloseHowTo = document.getElementById('btn-close-how-to');
const containerHowTo = document.getElementById('how-to-container');

btnOpenHowTo.addEventListener("click", function() {
    containerHowTo.classList.remove('hidden');
});

btnCloseHowTo.addEventListener("click", function() {
    containerHowTo.classList.add('hidden');
});