// Image Preview
const previews = [
    {
        input: document.getElementById('image'),
        preview: document.getElementById('image-preview')
    },
    {
        input: document.getElementById('edit-avatar-input'),
        preview: document.getElementById('edit-avatar')
    }
];

previews.forEach(item => {
    try {
        if (item.input && item.preview) {
            item.input.onchange = (e) => {
                if (item.input.files && item.input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        item.preview.src = e.target.result;
                    };
                    reader.readAsDataURL(item.input.files[0]);
                }
            };
        }
    } catch (error) {

    }
});