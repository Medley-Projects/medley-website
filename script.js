const root = document.documentElement;
const marqueeElementsDisplayed = getComputedStyle(root).getPropertyValue("--marquee-elements-displayed");
const marqueeContent = document.querySelector("ul.marquee-content");

root.style.setProperty("--marquee-elements", marqueeContent.children.length);

for (let i = 0; i < marqueeElementsDisplayed; i++) {
    marqueeContent.appendChild(marqueeContent.children[i].cloneNode(true));
}

function sendEmail() {
    var link = document.getElementById('send_email');
    var name = document.getElementById('fullname').value;
    var subject = document.getElementById('subject').value;
    var phone = document.getElementById('phone').value;
    var message = document.getElementById('message').value;
    var body = `${message}%0D%0A%0D%0AThanks,%0D%0A${name}%0D%0A${phone}`;
    var email = "info@medleynetworks.com"; 
    var href = "mailto:" + email + "?subject=" + subject + "&body=" + body;
    link.setAttribute("href", href);
}