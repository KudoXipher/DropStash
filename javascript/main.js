const fileInput = document.getElementById("fileInput");
        const bar = document.getElementById("bar");
        const percent = document.getElementById("percent");
        const chooseFileBtn = document.getElementById("chooseFileBtn");
        const cancelBtn = document.getElementById("cancelBtn");
        const uploadContainer = document.getElementById("upload-container");
        const dragDropArea = document.getElementById("drag-drop-area");
        const OrArea = document.getElementById("or-area");
        const fileNameElement = document.getElementById("file-name");
        const fileSizeElement = document.getElementById("file-size");

        let xhr;

        fileInput.addEventListener("change", () => {
            const file = fileInput.files[0];
            if (file) {
                uploadFile(file);
            }
        });

        uploadContainer.addEventListener("dragenter", (e) => {
            e.preventDefault();
            uploadContainer.classList.add("dragover");
        });

        uploadContainer.addEventListener("dragover", (e) => {
            e.preventDefault();
        });

        uploadContainer.addEventListener("dragleave", () => {
            uploadContainer.classList.remove("dragover");
        });

        uploadContainer.addEventListener("drop", (e) => {
            e.preventDefault();
            uploadContainer.classList.remove("dragover");
            const file = e.dataTransfer.files[0];
            if (file) {
                uploadFile(file);
            }
        });

        cancelBtn.addEventListener("click", () => {
            if (xhr && xhr.readyState !== XMLHttpRequest.DONE) {
                xhr.abort();
            }
            location.reload(); // Reload the page to start over
        });

        function uploadFile(file) {
            OrArea.style.display = "none";
            dragDropArea.style.display = "none";
            chooseFileBtn.style.display = "none";
            cancelBtn.style.display = "inline-block";
            
            xhr = new XMLHttpRequest();
    xhr.open("POST", "", true);

    xhr.onload = function () {
    if (xhr.status === 200) {
        // Insert the response HTML into the page
        document.getElementById('upload-container').innerHTML = xhr.responseText;
    } else {
        alert('File upload failed');
    }
};

            xhr.upload.addEventListener("progress", (event) => {
        const progress = (event.loaded / event.total) * 100;
        bar.style.width = `${progress}%`;
        percent.innerText = `${Math.round(progress)}%`;

        if (progress === 100) {

            fileNameElement.textContent = file.name;
            fileSizeElement.textContent = formatFileSize(file.size);

            resetProgress();
            setTimeout(() => {
                percent.innerText = "";
            }, 1000); // Reset progress after 1 second
        }
    });

    const formData = new FormData();
    formData.append("file", file);
    xhr.send(formData);
}


        function resetProgress() {
            chooseFileBtn.style.display = "inline-block";
            cancelBtn.style.display = "none";
            bar.style.width = "0";
            percent.innerText = "0%";
        }
        function copyToClipboard(element) {
    var text = element.value;  // Get the text from the input
    var input = document.createElement('input');  // Create a temporary input outside of the DOM
    input.value = text;  // Set its value to the text
    document.body.appendChild(input);  // Add it to the body
    input.select();  // Select it
    document.execCommand('copy');  // Copy it
    document.body.removeChild(input);  // Remove it from the DOM
}
const menuIcon = document.getElementById('menuIcon');
const navLinks = document.getElementById('navLinks');

menuIcon.addEventListener('click', () => {
    navLinks.classList.toggle('active');
});