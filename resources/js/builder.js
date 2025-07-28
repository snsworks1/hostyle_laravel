import grapesjs from 'grapesjs';
import 'grapesjs/dist/css/grapes.min.css';

document.addEventListener('DOMContentLoaded', function () {
    grapesjs.init({
        container: '#gjs',
        height: '100vh',
        width: 'auto',
        storageManager: { type: null },
    });
});
