const newTicket = document.querySelector('#new-ticket');
const ticket = document.querySelector('#ticket-main');

if (newTicket || ticket) {
    let dbtags = [];

    window.onload = async () => {
        const url = '../api/api_tags.php';
        const response = await fetch(url);
        const allTags = await response.json();
        dbtags = allTags.map(tag => tag.name).filter(Boolean);
    };

    const input = document.querySelector('#tags');
    if (input) {
        let matchingTags = [];
        let index = 0;
        
        input.addEventListener('input', function (event) {
            const tag = input.value.toUpperCase();
        
            if (tag === '') return;
        
            matchingTags = dbtags.filter(dbtag => dbtag && dbtag.toUpperCase().startsWith(tag)).filter(Boolean);
        });

        input.addEventListener('keydown', async function (event) {
            let exist = false;

            if (event.key === 'Tab') {
                event.preventDefault();
                if (matchingTags.length > 0) {
                    input.value = matchingTags[index];
                    index = (index + 1) % matchingTags.length;
                }
            }

            if (ticket && event.key === 'Enter') {
                event.preventDefault();
                const tagName = input.value;

                if (document.querySelector('#' + tagName)) {
                    input.value = "";
                } else {
                    const button = document.createElement('button');
                    button.formAction = '../actions/action_delete_ticket_tag.php';
                    button.formMethod = 'post';
                    button.classList.add('all-tags');
                    button.name = 'name';
                    button.textContent = tagName;
                    button.id = tagName;

                    const section = document.querySelector('#property-tag');
                    section.insertBefore(button, input);

                    for (const tag of dbtags) {
                        if (tag === tagName) {
                            input.value = "";
                            exist = true;
                        }
                    }

                    let success = false;
                    if (!exist) {
                        const url = '../api/api_tags.php/';
                        const data = { name: tagName };
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: encodeForAjax(data),
                        })
                        success = await response.json();
                    }

                    if (success || exist) {
                        dbtags.push(tagName);
                        const id = document.querySelector('#id');
                        const url = '../api/api_ticket.php';
                        const data = { id: id.value, tag: tagName };
                        const response = await fetch(url, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: encodeForAjax(data),
                        });
                    }

                    input.value = "";
                }
            }
        });
    }
}
