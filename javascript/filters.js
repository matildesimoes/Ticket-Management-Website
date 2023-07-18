function printTickets(tickets, limit, offset) {
    const section = document.querySelector('#tickets');
    section.innerHTML = '';

    const h2 = document.createElement('h2')
    h2.textContent = 'Tickets'

    for (let i = 0; i < limit && i + offset < tickets.length; i++) {
        const ticket = tickets[i + offset];
        const article = document.createElement('article');
        article.classList.add('ticket');

        const a = document.createElement('a');
        a.href = '../pages/ticket.php?id=' + ticket.id;

        const header = document.createElement('header');
        header.classList.add('author');

        const img = document.createElement('img');
        img.src = '../profile_photos/' + ticket.author.photo;
        img.alt = 'Profile Photo';
        header.appendChild(img);

        const h3 = document.createElement('h3');
        h3.textContent = ticket.author.firstName + ' ' + ticket.author.lastName;
        header.appendChild(h3);

        a.appendChild(header);

        const h4 = document.createElement('h4');
        h4.textContent = ticket.title;
        a.appendChild(h4);

        if (ticket.status) {
            const pStatus = document.createElement('p');
            pStatus.classList.add('status', ticket.status.name.toLowerCase());
            pStatus.textContent = ticket.status.name;
            a.appendChild(pStatus);
        }

        const pDateOpened = document.createElement('p');
        pDateOpened.classList.add('date-opened');
        pDateOpened.textContent = ticket.dateOpened;
        a.appendChild(pDateOpened);

        const pPriority = document.createElement('p');
        if (ticket.priority) {
            pPriority.classList.add('priority', ticket.priority.name.toLowerCase());
            pPriority.textContent = ticket.priority.name;
        } else {
            pPriority.classList.add('priority');
            pPriority.textContent = 'None';
        }
        a.appendChild(pPriority);

        article.appendChild(a);

        section.appendChild(article);
    }
}

async function fetchTickets(after, before, status, priority, department, agent, tag) {
    const url = '../api/api_ticket.php?' + encodeForAjax({
        after: after.value,
        before: before.value,
        status: status.value,
        priority: priority.value,
        department: department.value,
        agent: agent.value,
        tag: tag.value
    });

    const response = await fetch(url);
    return await response.json();
}

const ticketsPage = document.querySelector('#tickets-page');

if (ticketsPage) {
    const after = document.querySelector('#after');
    const before = document.querySelector('#before');
    const status = document.querySelector('#status');
    const priority = document.querySelector('#priority');
    const department = document.querySelector('#department');
    const agent = document.querySelector('#agent');
    const tag = document.querySelector('#tag');

    const limit = 8; /* Math.floor(document.querySelector('#tickets').clientHeight / 75);*/
    let offset = 0;

    const paging = document.querySelector('#paging');
    paging.innerHTML = '';

    const previous = document.createElement('button');
    previous.id = 'previous';
    previous.textContent = 'Previous';
    previous.style.display = 'none'
    previous.addEventListener('click', async function (event) {
        event.preventDefault();
        offset = offset - limit;
        const tickets = await fetchTickets(after, before, status, priority, department, agent, tag);
        printTickets(tickets, limit, offset);
        if (offset <= 0)
            previous.style.display = 'none';
        else
            previous.style.display = 'block';
        document.querySelector('#next').style.display = 'block';
    })

    const next = document.createElement('button');
    next.id = 'next';
    next.textContent = 'Next';
    if (document.querySelectorAll('article').length === limit)
        next.style.display = 'block';
    else
        next.style.display = 'none';
    next.addEventListener('click', async function (event) {
        event.preventDefault();
        offset = offset + limit;
        const tickets = await fetchTickets(after, before, status, priority, department, agent, tag);
        printTickets(tickets, limit, offset);
        if (offset + limit >= tickets.length)
            next.style.display = 'none';
        else
            next.style.display = 'block';
        document.querySelector('#previous').style.display = 'block';
    })

    paging.appendChild(previous);
    paging.appendChild(next);

    const filter = document.querySelector('#filter');
    if (filter) {
        filter.addEventListener('click', async function (event) {
            event.preventDefault();
            const tickets = await fetchTickets(after, before, status, priority, department, agent, tag);
            offset = 0;
            printTickets(tickets, limit, offset);
            document.querySelector('#previous').style.display = 'none';
            if (limit < tickets.length)
                document.querySelector('#next').style.display = 'block';
            else
                document.querySelector('#next').style.display = 'none';
        })
    }
}
