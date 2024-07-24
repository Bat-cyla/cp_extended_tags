
let multiselect_block = document.querySelectorAll(".multiselect_block");
multiselect_block.forEach(parent => {
    let label = parent.querySelector(".field_multiselect");
    let select = parent.querySelector(".field_select");
    let text = label.innerHTML;
    select.addEventListener("click", function(element) {

        let selectedOptions = this.selectedOptions;
        for (let option of selectedOptions) {
                let button = document.createElement("button");
                button.type = "submit";
                button.id=option.value;
                button.className = "btn_multiselect";
                button.value = option.value;
                button.textContent = option.value;
                button.onclick = _ => {
                    option.selected = false;
                    button.remove();
                };
                label.appendChild(button);
        }
    })
    console.log(label)

})
