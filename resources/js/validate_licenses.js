
let contentVerifyLicensesEl = null, formVerifyLicense = null, formRetrieveLicense = null, btnVerifyLicenses = null, btnRetrieveLicenses = null, contentVerifyLicensesDataEl = null;

let requestOptions = {
    method  : 'GET',
    redirect: 'follow'
}

const promiseFetching = (url, options) => {
    return  new Promise((resolve, reject) => {
        fetch(url, options)
        .then(response => response.json())
        .then(result => {
            resolve(result)
        })
        .catch(error => reject(error));
    });
}

const loadValidateAll = () => {
    contentVerifyLicensesEl = document.querySelector('.content_verify_licenses')??null;
    if(contentVerifyLicensesEl!==null) {
        formVerifyLicense = contentVerifyLicensesEl.querySelector('#formVerifyLicense')??null;
        const nLicense = formVerifyLicense.querySelector('#n_license')??null;
        formRetrieveLicense = contentVerifyLicensesEl.querySelector('#formRetrieveLicense')??null;
        const token = formRetrieveLicense.querySelector('#token')??null;
        contentVerifyLicensesDataEl = contentVerifyLicensesEl.querySelector('.content_verify_licenses-data')??null;

        // ? Botón para verificar licencia
        btnVerifyLicenses = contentVerifyLicensesEl.querySelector('.btn_verify_licenses')??null;
        if( btnVerifyLicenses !== null ) {
            btnVerifyLicenses.addEventListener('click', (e) => {
                if(formVerifyLicense!==null)formVerifyLicense.classList.replace('d-none', 'd-block');
                if(formRetrieveLicense!==null)formRetrieveLicense.classList.replace('d-block', 'd-none');
                btnVerifyLicenses.classList.replace('inactive', 'active');
                btnRetrieveLicenses.classList.replace('active', 'inactive');
                if(contentVerifyLicensesDataEl!==null)contentVerifyLicensesDataEl.innerHTML = '';
                if(nLicense!==null)nLicense.value = '';
                if(token!==null)token.value = '';
            })
        }
        // ? Botón para recuperar licencia
        btnRetrieveLicenses = contentVerifyLicensesEl.querySelector('.btn_retrieve_licenses')??null;
        if( btnRetrieveLicenses !== null ) {
            btnRetrieveLicenses.addEventListener('click', (e) => {
                if(formRetrieveLicense!==null)formRetrieveLicense.classList.replace('d-none', 'd-block');
                if(formVerifyLicense!==null)formVerifyLicense.classList.replace('d-block', 'd-none');
                btnVerifyLicenses.classList.replace('active', 'inactive');
                btnRetrieveLicenses.classList.replace('inactive', 'active');
                if(contentVerifyLicensesDataEl!==null)contentVerifyLicensesDataEl.innerHTML = '';
                if(nLicense!==null)nLicense.value = '';
                if(token!==null)token.value = '';
            })
        }

        if(formVerifyLicense!==null) {
            const btnVerify = formVerifyLicense.querySelector('button')??null;
            if( btnVerify !== null ) {
                btnVerify.addEventListener('click', (e) => {
                    if( nLicense !== null ) {
                        const valNLicense = nLicense.value??'';
                        if( valNLicense !== '' ) {
                            nLicense.value = valNLicense.trim();
                            let data = {
                                "id": valNLicense.trim(),
                                "type": 0,
                                "type_req": "license",
                            };
                            promiseFetching("/api/verify_license", {
                                method: 'POST',
                                body: JSON.stringify(data),
                                headers:{
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => {
                                const { status, message, data } = response
                                if(contentVerifyLicensesDataEl!==null ) {
                                    let {status,remaining_days,uri_domain,token,start_date,finish_date} = data;
                                    const strData = JSON.stringify(data);
                                    let strFormatData = strData.replaceAll('{', '{ \n\n').replaceAll('}', ' \n\n}').replaceAll(', ', ', \n\n');
                                    const clsBadge = status == 'active' ? 'bg-success' : 'bg-danger';
                                    contentVerifyLicensesDataEl.innerHTML = `<div class="data_result">
                                        <h5>${message}</h5>
                                        <p><strong>Estado de la licencia:</strong> <span class="badge ${clsBadge}">${status??'--'}</span></p>
                                        <p><strong>Domino registrado:</strong> ${uri_domain??'--'}</p>
                                        <p><strong>Token registrado:</strong> ${token??'--'}</p>
                                        <p><strong>Fecha de inicio:</strong> ${start_date??'--'}</p>
                                        <p><strong>Fecha de finalización:</strong> ${finish_date??'--'}</p>
                                        <p class="mb-4"><strong>Días restantes:</strong> ${remaining_days??'--'}</p>
                                        <code>${strData??''}</code>
                                    </div>`;
                                }
                            })
                            .catch(error => console.log('error', error));
                        }
                    } 
                })
            }
        }
        if(formRetrieveLicense!==null) {
            const btnRetrieve = formRetrieveLicense.querySelector('button')??null;
            if( btnRetrieve !== null ) {
                btnRetrieve.addEventListener('click', (e) => {
                    if( token !== null ) {
                        const valToken = token.value;
                        if( valToken !== '' ) {
                            let data = {
                                "id": valToken,
                                "type": 0,
                                "type_req": "token",
                            };
                            promiseFetching("/api/verify_license", {
                                method: 'POST',
                                body: JSON.stringify(data),
                                headers:{
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => {
                                const { status, message, data } = response
                                if(contentVerifyLicensesDataEl!==null ) {
                                    const strData = JSON.stringify(data);
                                    let strFormatData = strData.replaceAll('{', '{ \n\n').replaceAll('}', ' \n\n}').replaceAll(', ', ', \n\n');
                                    contentVerifyLicensesDataEl.innerHTML = `<div class="data_result">
                                        <h5>${message}</h5>
                                        <p class="mb-4"><strong>Licencia registrada:</strong> ${data.license??'--'}</p>
                                        <code>${strData??''}</code>
                                    </div>`;
                                }
                            })
                            .catch(error => console.log('error', error));
                        }
                    } 
                })
            }
        }
    }
}
export const contentVerifyLicensesInit = () => {
    console.clear();
    loadValidateAll();
}


