import axios from 'axios';
import $ from 'jquery';
window.$ = $;
window.jQuery = $;

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
