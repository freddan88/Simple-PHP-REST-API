"use strict";

const form = document.getElementById("contact-form");
const globalMessages = form.querySelector("#status-global");

const formData = new FormData();
formData.append("email_from", myEmail);
formData.append("subject", mySubject);
formData.append("apikey", myApikey);

const setFieldErrors = (obj) => {
    for (const property in obj) {
        form.querySelector(`#status-${property}`).textContent = obj[property];
        setTimeout(() => {
            form.querySelector(`#status-${property}`).textContent = "";
        }, 3000);
    }
};

const setGlobalMessage = (obj) => {
    globalMessages.textContent = obj.message;
    if (obj.success) {
        return (globalMessages.classList = "alert alert-success");
    }
    globalMessages.classList = "alert alert-danger";
};

const sendEmail = async () => {
    form.classList.add("disabled");
    const res = await axios(emailEndpoint, {
        method: "post",
        data: formData,
    });
    if (!res.data) return;
    if (res.data.field_errors) {
        const obj = res.data.field_errors;
        return setFieldErrors(obj);
    }
    if (res.data.global_status) {
        const obj = res.data.global_status;
        return setGlobalMessage(obj);
    }
};

const handleSubmit = (e) => {
    globalMessages.classList.replace("alert-light", "alert-info");
    globalMessages.textContent = "Trying to send email";
    formData.set("email_to", form.elements[1].value);
    formData.set("message", form.elements[2].value);
    formData.set("name", form.elements[0].value);
    e.preventDefault();
    sendEmail();
    setTimeout(() => {
        globalMessages.classList = "alert alert-light";
        globalMessages.textContent = "";
        form.classList.remove("disabled");
    }, 4000);
};

form.addEventListener("submit", handleSubmit);
