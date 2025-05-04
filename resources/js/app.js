import { animate, inView } from "motion";
import * as FilePond from 'filepond';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';

window.animate = animate;
window.inView = inView;
window.FilePond = FilePond;
window.FilePondPluginFileValidateType = FilePondPluginFileValidateType;
window.FilePondPluginFileValidateSize = FilePondPluginFileValidateSize;
window.FilePondPluginImagePreview = FilePondPluginImagePreview;

window.addEventListener('livewire:navigating', () => {
    Livewire.dispatch('action-toast-closed');
}, { once: true });

window.addEventListener('livewire:navigated', () => {
    document.addEventListener('alpine:init', () => {
        window.Alpine.directive('collapse', (el, {}, { effect, cleanup }) => {
            let duration = 200

            effect(() => {
                let currentMaxHeight = el.style.maxHeight
                el.style.transition = `max-height ${duration}ms ease-out`
                el.style.overflow = 'hidden'

                if (!el._x_isShown) {
                    el.style.maxHeight = '0px'
                } else if (el._x_isShown) {
                    el.style.maxHeight = `${el.scrollHeight}px`
                }
            })

            cleanup(() => {
                el.style.maxHeight = null
                el.style.transition = null
                el.style.overflow = null
            })
        })
    })
}, { once: true });

