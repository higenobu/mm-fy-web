document.addEventListener("DOMContentLoaded", () => {
    console.log("JavaScript loaded successfully!");

    // Example: Attach event listeners to forms or buttons
    const createForm = document.querySelector("#createMemoForm");
    if (createForm) {
        createForm.addEventListener("submit", validateCreateForm);
    }

    const deleteButtons = document.querySelectorAll(".delete-button");
 console.log(`Found ${deleteButtons.length} delete buttons.`);
    deleteButtons.forEach((button) => {
        button.addEventListener("click", handleDelete);
    });
});
function validateCreateForm(event) {
    // Prevent form submission for validation
    event.preventDefault();

    const title = document.querySelector("#title").value.trim();
    const comment = document.querySelector("#comment").value.trim();

    // Basic validation
    if (!title) {
        alert("The title field cannot be empty.");
        return;
    }
    if (!comment) {
        alert("The comment field cannot be empty.");
        return;
    }

    // Allow the form to be submitted
    event.target.submit();
}
//function to handle deletion
document.addEventListener("DOMContentLoaded", () => {
    // Select all delete buttons
    const deleteButtons = document.querySelectorAll(".delete-button");

    // Attach a click event handler to each delete button
    deleteButtons.forEach((button) => {
        button.addEventListener("click", handleDelete);
    });
});

// Function to handle deletion


document.addEventListener("DOMContentLoaded", () => {
    // Attach event listeners to all delete buttons
    const deleteButtons = document.querySelectorAll(".delete-button");

    deleteButtons.forEach((button) => {
        button.addEventListener("click", handleDelete);
    });
});

async function handleDelete(event) {
    const button = event.target;
    const memoId = button.getAttribute("data-id"); // Get memo ID from button

    if (!confirm("Are you sure you want to delete this memo?")) {
        return; // Exit if user cancels
    }

    try {
        // Send delete request to the server
        const response = await fetch(`/memos/delete`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: memoId }),
        });

        if (!response.ok) {
            throw new Error("Failed to delete.");
        }

        // Remove the memo from the DOM
        const memoElement = document.querySelector(`#memo-${memoId}`);
        if (memoElement) {
            memoElement.remove();
        }

        alert("Memo deleted successfully.");
    } catch (error) {
        console.error("Error deleting memo:", error);
        alert("Unable to delete the memo. Please try again.");
    }
}

