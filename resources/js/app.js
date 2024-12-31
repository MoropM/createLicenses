/*require('./bootstrap');*/


const formCreateLicense = document.querySelector('#formCreateLicense') || null;
if( formCreateLicense !== null ) {
    import('./create_licenses.js')
    .then( response => {
        const { default: formCreateLicense } = response
        formCreateLicense()
    })
    .catch(error => console.error('Error:', error))
}
