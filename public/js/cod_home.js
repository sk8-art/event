const button = document.querySelector(".btn--add-post"); 

const div = document.querySelector(".post");

button.addEventListener("click", () => {
    if (div.style.display == 'none') {
        div.style.display = 'flex';
    } else {
        div.style.display = 'none';
    }
});

document.addEventListener("click", (event) => {
    if (!div.contains(event.target) && event.target !== button) {
        div.style.display = 'none';
    }
});
