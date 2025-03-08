import { set } from "lodash";

let formCreateLicense = null, inputUri = null, inputDate = null, inputDays = null;

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
const verificarURL = async(url) => {
    try {
        const respuesta = await fetch(url, {
            method: 'HEAD'
        });
        return respuesta.ok
    } catch (error) {
        return false;
    }
}

const deleteErrorMessage = (input, removeErr = 0) => {
    const contentInput = input.parentElement
    const msgError = contentInput.querySelector(`#${input.id}Err`)
    if( msgError !== null ) contentInput.removeChild(msgError);
    if(removeErr==1)input.classList.remove('is-invalid')
}
const createErrorMessage = (input, strMsg) => {
    const contentInput = input.parentElement
    const idError = `${input.id}Err`
    deleteErrorMessage(input)

    const errorMsg = document.createElement(`span`)
    errorMsg.id = idError
    errorMsg.classList.add(`error-message`, `invalid-feedback`)
    errorMsg.textContent = strMsg
    contentInput.appendChild(errorMsg)

}
const clearFormLicense = () => {
    inputUri.classList.remove('is-valid');
    inputDate.classList.remove('is-valid');
    inputDays.classList.remove('is-valid');
    formCreateLicense.reset();
}
const addEventsTRows = (tBodyContent) => {
    const trRows = tBodyContent.querySelectorAll('tr') || [];
    trRows.forEach(tr => {
        // ? Buscamos el botón de copiar el token
        const btnCopy = tr.querySelector('.btn_copy') || null;
        if( btnCopy !== null ) {
            btnCopy.addEventListener('click', (e) => {
                const dataTk = btnCopy.dataset.tk;
                if( dataTk !== undefined && dataTk !== null && dataTk !== '' ) {
                    const dataTkCopy = dataTk.split('.')[1];
                    const copyClipB = navigator.clipboard?.writeText(dataTkCopy);
                    if( copyClipB === undefined ) {
                        let tmpTxtcopy = document.createElement("input");
                        tmpTxtcopy.type = "text";
                        tmpTxtcopy.setAttribute("style", "position: absolute; left: -1000px;");
                        tmpTxtcopy.value = dataTkCopy;
                        document.body.appendChild(tmpTxtcopy);
                        tmpTxtcopy.select();
                        document.execCommand("copy");
                        setTimeout(() => tmpTxtcopy.remove() , 500);
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Token copiado al portapapeles',
                        text: `●●●●●●●●●${dataTkCopy.slice(-5)}`,
                        confirmButtonText: `Aceptar`,
                        timer: 1500,
                        showConfirmButton: false,
                        showCloseButton: true,
                        focusConfirm: false,
                        allowOutsideClick: false,
                    })
                }
            })
        }
    })
}
const insertNewTr = (result, type) => {
    const tBodyContent = document.querySelector('#tBodyContent')
    const dataResult = result.data

    if( type == 0 ) {
        dataResult.forEach(element => {
            const clsBadge = element.status == 'active' ? 'bg-success' : 'bg-danger';
            let existUri = false;
            verificarURL(element.uri)
                .then(result => existUri = result )
                .catch(error => console.warn(error));
            const strUri = existUri ? `<a href="${element.uri}" target="_blank">${element.uri}</a>` : element.uri;
            
            let trBody = ''
            trBody += `
                <tr>
                    <td>
                        <div class="sec_content">${strUri}</div>
                    </td>
                    <td>
                        <div class="sec_content">${element.start_date}</div>
                    </td>
                    <td>
                        <div class="sec_content">${element.finish_date}</div>
                    </td>
                    <td>
                        <div class="sec_content">${element.license_number}</div>
                    </td>
                    <td>
                        <div class="sec_content">
                            <span>●●●●●●●●●${element.token.slice(-5)}</span>
                            <button type="button" class="btn py-0 px-0 btn_copy" data-tk="${new Date().getTime()}.${element.token}.e72ew2${new Date().getTime()}" ><svg  xmlns="http://www.w3.org/2000/svg"  width="15"  height="15"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg></button>
                        </div>
                    </td>
                    <td>
                        <div class="sec_content center">
                            <span class="badge ${clsBadge}">${element.status}</span>
                        </div>
                    </td>
                </tr>
            `
            tBodyContent.insertRow(-1).innerHTML = trBody
        });
    }
    if( type == 1 ){
        let existUri = false;
        verificarURL(dataResult.uri)
            .then(result => existUri = result )
            .catch(error => console.warn(error));
        const strUri = existUri ? `<a href="${dataResult.uri}" target="_blank">${dataResult.uri}</a>` : dataResult.uri;
        const clsBadge = dataResult.status == 'active' ? 'bg-success' : 'bg-danger';
        let trBody = ''
        trBody += `
            <tr>
                <td>
                    <div class="sec_content">${strUri}</div>
                </td>
                 <td>
                    <div class="sec_content">${dataResult.start_date}</div>
                </td>
                <td>
                    <div class="sec_content">${dataResult.finish_date}</div>
                </td>
                <td>
                    <div class="sec_content">${dataResult.license_number}</div>
                </td>
                <td>
                        <div class="sec_content center">
                            <span>●●●●●●●●●${dataResult.token.slice(-5)}</span>
                            <button type="button" class="btn py-0 px-0 btn_copy" data-tk="${new Date().getTime()}.${dataResult.token}.2eew2${new Date().getTime()}" ><svg  xmlns="http://www.w3.org/2000/svg"  width="15"  height="15"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-copy"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 7m0 2.667a2.667 2.667 0 0 1 2.667 -2.667h8.666a2.667 2.667 0 0 1 2.667 2.667v8.666a2.667 2.667 0 0 1 -2.667 2.667h-8.666a2.667 2.667 0 0 1 -2.667 -2.667z" /><path d="M4.012 16.737a2.005 2.005 0 0 1 -1.012 -1.737v-10c0 -1.1 .9 -2 2 -2h10c.75 0 1.158 .385 1.5 1" /></svg></button>
                        </div>
                    </td>
                <td>
                    <span class="badge ${clsBadge}">${dataResult.status}</span>
                </td>
            </tr>
        `
        tBodyContent.insertRow(-1).innerHTML = trBody
    }

    setTimeout(() => {
        addEventsTRows(tBodyContent);
    }, 300);
}

const getLicenses = (id) => {
    if( id == 0 ) {
        // fetch("/api/licences", requestOptions)
        // .then(response => response.json())
        promiseFetching("/api/licences", requestOptions)
        .then(function (result) {
            if( result != '' || result != null ) { insertNewTr(result, 0) }
        })
        .catch(error => console.log('error', error));
    }
}
/** Crear licencia
 * 
 * @param {*} inputUri 
 * @param {*} inputDate 
 */
const sendData = (inputUri, inputDate) => {
    let data = {
        "uri_access": inputUri,
        "finishDate": inputDate
    };
    // ? Creación de licencia
    promiseFetching("/api/license_create",  {
        method: 'POST',
        body: JSON.stringify(data),
        headers:{
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if( Reflect.has(response, 'data') && response.status ) {
            setTimeout(() => {
                clearFormLicense();
                insertNewTr(response, 1);
            }, 150);
        }
        else {
            let titleStr = '', messageStr = ''
            if( Reflect.has(response, 'errors') ) {
                const { errors } = response
                if( Reflect.has(errors,'uri_access') ) {
                    const { uri_access } = errors
                    if( Array.isArray(uri_access) ) {
                        document.querySelector(`#uriGen`).classList.replace('is-valid', 'is-invalid')
                        messageStr = errors.uri_access[0]
                    }
                }
                else messageStr = response.errors
            }
            titleStr = response.message
            Swal.fire({
                icon: 'error',
                title: titleStr,
                text: messageStr,
                confirmButtonText: `Aceptar`,
            })
        }
    })
    .catch(error => console.error('Error:', error));
    
}

const validateUrl = (value) => {
    return /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:[/?#]\S*)?$/i.test(value);
}
const validateDate = (dateVal) => {
    let splitDate = dateVal.split('-')
    let dd = '', mm = '', yy = ''
    dd = splitDate[2], mm = splitDate[1], yy = splitDate[0]
    // String with valid date separated by dash
    let srtDate = `${dd}-${mm}-${yy}`
    // Regular expression to check if string is valid date
    const regexExp = /(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[13-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})/gi;
    let resultDate = regexExp.test(srtDate)  // true
    
    return resultDate

}
/** Validación del formulario de creación de licencia
 * 
 * @param {*} form 
 * @returns 
 */
const validateForm = (form) => {
    let contError = 0
    const inputsForm = form.querySelectorAll('input')
    inputsForm.forEach(input => {
        deleteErrorMessage(input)
        if( input.type != 'hidden' && input.type != 'text' ){
            let inputValue = input.value
            let inputId = input.id
            if( inputValue.trim() == '' && input.type != 'number' ){
                input.classList.add('is-invalid')
                createErrorMessage(input, `El campo no puede quedar vacío`)
                contError += 1
            }
            else { 
                input.classList.add('is-valid')
                if( inputId == 'uriGen' ) {
                    if( !validateUrl(inputValue.trim()) ){
                        input.classList.remove('is-valid')
                        input.classList.add('is-invalid')
                        createErrorMessage(input, `La URL es inválida`)
                        contError += 1
                    }
                    else input.classList.add('is-valid')
                }
                if( inputId == 'dateEnd' ) {
                    if( !validateDate(inputValue.trim()) ){
                        input.classList.remove('is-valid')
                        input.classList.add('is-invalid')
                        contError += 1
                    }
                    else input.classList.add('is-valid')
                }
            }
        }
    });
    return contError
}
/** Calcula la fecha de finalización a partir de la fecha actual y el n° de días
 * 
 * @param {*} days 
 */
const addDayAtDate = days => {
    let currentDate = new Date();
    currentDate.setDate(currentDate.getDate() + parseInt(days));
    let currentYear = currentDate.getFullYear();
    let currentMonth = currentDate.getMonth() + 1;
    if( parseInt(currentMonth) < 10 ) currentMonth = `0${currentMonth}`;
    let currentDay = currentDate.getDate();
    if( parseInt(currentDay) < 10 ) currentDay = `0${currentDay}`;

    inputDate.value = `${currentYear}-${currentMonth}-${currentDay}`;
    deleteErrorMessage(inputDate,1);
}

const loadAll = () => {
    formCreateLicense = document.querySelector('#formCreateLicense') || null;
    inputUri = formCreateLicense.querySelector('#uriGen') || null;
    inputDate = formCreateLicense.querySelector('#dateEnd') || null;
    inputDays = formCreateLicense.querySelector('#dayEl') || null;

    let today = new Date()
    let year = today.getFullYear()
    let month = today.getMonth() + 1, srtMonth = ''
    let day = today.getDate(), srtDay = ''
    if( month >= 1 && month <= 9 ) { srtMonth = `0${month}` }
    else srtMonth = month
    if( day >= 1 && day <= 9 ) { srtDay = `0${day}` }
    else srtDay = day
    
    inputDate.setAttribute('min', `${year}-${srtMonth}-${srtDay}`)


    if( formCreateLicense !== null ) {
        formCreateLicense.addEventListener('click', (e) => {
            if( e.target && e.target.tagName == 'BUTTON' ){
                if( validateForm(formCreateLicense) == 0 ){
                    sendData(inputUri.value,inputDate.value)
                }
                
            }
        })
        formCreateLicense.addEventListener('keyup', (e) => {
            if( e.target && e.target.tagName == 'INPUT' ){
                const inputElement = e.target;
                const inputId = inputElement.id;
                deleteErrorMessage(inputElement,1);
                if( inputId == 'uriGen' ) inputElement.classList.remove('is-invalid');
                if( inputElement.type == "number" ) {
                    const valDays = inputElement.value;
                    if(parseInt(valDays)>0)addDayAtDate(valDays);
                    else {
                        inputElement.value = '';
                        inputDate.value = '';
                    }
                }
            }
        })
        formCreateLicense.addEventListener('change', (e) => {
            if( e.target && e.target.tagName == 'INPUT' ){
                const inputElement = e.target
                const inputId = inputElement.id;
                deleteErrorMessage(inputElement,1);
                if( inputId == 'dateEnd' ) inputElement.classList.remove('is-invalid');
                if( inputElement.type == "number" ) {
                    const valDays = inputElement.value;
                    if(parseInt(valDays)>0)addDayAtDate(valDays);
                    else {
                        inputElement.value = '';
                        inputDate.value = '';
                    }
                }
            }
        })
    }
    getLicenses(0)

}

export default formCreateLicense = () => {
    console.clear();
    loadAll();
}
