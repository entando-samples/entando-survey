import { quillEditor } from 'vue-quill-editor';
import Quill from 'quill';
import client from '@/http'


// let BlockEmbed = Quill.import('blots/block/embed');

// class ImageBlot extends BlockEmbed { }
// ImageBlot.blotName = 'image';
// ImageBlot.tagName = 'img';

// Quill.register(ImageBlot);


const editorOptions = {
    modules: {
        toolbar: {
            container: [
                [{ 'size': ['small', '', 'large', 'huge'] }, 'bold', 'italic', 'underline',],
                [{ align: '' }, { align: 'center' }, { align: 'right' }, { align: 'justify' }],
                [{ 'indent': '-1' }, { 'indent': '+1' }],
                ['link', 'image'],
            ],
            handlers: { image: quill_img_handler }
        }
    },
    placeholder: 'Write here...',
    theme: 'snow'
};

function quill_img_handler() {
    let fileInput = this.container.querySelector('input.ql-image[type=file]');

    if (fileInput == null) {
        fileInput = document.createElement('input');
        fileInput.setAttribute('type', 'file');
        fileInput.setAttribute('accept', 'image/png, image/gif, image/jpeg, image/bmp, image/x-icon');
        fileInput.classList.add('ql-image');
        fileInput.addEventListener('change', () => {
            const files = fileInput.files;
            const range = this.quill.getSelection(true);

            if (!files || !files.length) {
                console.log('No files selected');
                return;
            }

            const formData = new FormData();
            formData.append('file', files[0]);

            this.quill.enable(false);

            client
                .post('uploads', formData)
                .then(response => {
                    this.quill.enable(true);
                    this.quill.editor.insertEmbed(range.index, 'image', response.data);
                    this.quill.setSelection(range.index + 1, Quill.sources.SILENT);
                    fileInput.value = '';
                })
                .catch(error => {
                    console.log('quill image upload failed');
                    console.log(error);
                    this.quill.enable(true);
                });
        });
        this.container.appendChild(fileInput);
    }
    fileInput.click();
}

export {
    quillEditor,
    editorOptions
}
