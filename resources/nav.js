// ------------ responsive navigation bar

function toggleMobileMenu(menu) {
    menu.classList.toggle('open');
}

// -------------- New Documnet Form field validation

function validateForm() {
    var type = document.getElementById("type").value;
    var customer = document.getElementById("customer_id").value;
    var category = document.getElementById("category").value;

    if (type === "") {
        alert("Please select a Document type!");
        return false;
    }

    if (customer === "") {
        alert("Please select a Customer!");
        return false;
    }

    if (category === "") {
        alert("Please select a Category!");
        return false;
    }

    return true;
}

// function validateForm() {
//     var customer = document.getElementById("cust_id").value;
//     var category = document.getElementById("cat_id").value;
//     if (customer === "") {
//         alert("Please select a Customer!");
//         return false;
//     }
//     if (category === "") {
//         alert("Please select a Category!");
//         return false;
//     }
//     return true;
// }

// ------------- Function to calculate the total

function calculateTotal() {
    var trainees = parseInt(document.getElementById("trainees").value);
    var costPerTrainee = parseFloat(document.getElementById("cost_per_trainee").value);
    var total = trainees * costPerTrainee;
    document.getElementById("total").value = total.toFixed(2);
}

// ------------ Attach event listeners to both input fields

document.getElementById("trainees").addEventListener("input", calculateTotal);
document.getElementById("cost_per_trainee").addEventListener("input", calculateTotal);

// --------- Calculate the total when the page loads
window.onload = function () {
    calculateTotal();
};

//--------- Update order/task status

function updateStatus(status, rfq_id) {
    // AJAX call to update status
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // Update status color in the table
            var statusTd = document.querySelector('[data-ord-id="' + ord_id + '"] .status');
            var newColor = getStatusColor(status);
            statusTd.style.backgroundColor = newColor;
        }
    };
    xhr.open("POST", "update_status.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("status=" + status + "&ord_id=" + ord_id);
}
