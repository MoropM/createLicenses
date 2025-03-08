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


const contentVerifyLicenses = document.querySelector('.content_verify_licenses') || null;
if( contentVerifyLicenses !== null ) {
    import('./validate_licenses.js')
    .then( response => {
        console.log(  response  );
        const { contentVerifyLicensesInit } = response
        contentVerifyLicensesInit()
    })
    .catch(error => console.error('Error:', error))
}
