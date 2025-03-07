$(document).ready(function () {
    $('.states').select2({
        minimumResultsForSearch: Infinity
    });
})
// get kod tn ved from corn's id crops_name
const kodtnved = document.getElementById('kodtnved');
const stateDropdown = document.getElementById('crops_name');

stateDropdown.addEventListener('change', () => {
    const stateId = stateDropdown.value;
    if(stateId){
        fetch(`/getkodtnved/${stateId}`)
            .then(response => response.json())
            .then(data => kodtnved.value = data.code);
    }
});
