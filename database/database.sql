PRAGMA foreign_keys = ON;

/* DROP */

DROP TABLE IF EXISTS TicketTag;
DROP TABLE IF EXISTS AgentDepartment;
DROP TABLE IF EXISTS Message;
DROP TABLE IF EXISTS Change;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS FAQ;
DROP TABLE IF EXISTS Status;
DROP TABLE IF EXISTS Tag;
DROP TABLE IF EXISTS Priority;
DROP TABLE IF EXISTS Department;
DROP TABLE IF EXISTS Admin;
DROP TABLE IF EXISTS Agent;
DROP TABLE IF EXISTS User;


/* CREATE */

CREATE TABLE User (
    idUser INTEGER NOT NULL,
    firstName TEXT NOT NULL,
    lastName TEXT NOT NULL,
    username TEXT NOT NULL,
    email TEXT NOT NULL,
    password TEXT NOT NULL,
    photo TEXT DEFAULT 'profile_default.png',
    CONSTRAINT UserPK PRIMARY KEY (idUser),
    CONSTRAINT UserUsernameCK UNIQUE (username),
    CONSTRAINT UserEmailCK UNIQUE (email)
);

CREATE TABLE Agent (
    idAgent INTEGER NOT NULL,
    CONSTRAINT AgentPK PRIMARY KEY (idAgent),
    CONSTRAINT AgentUserFK FOREIGN KEY (idAgent) REFERENCES User (idUser) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Admin (
    idAdmin INTEGER NOT NULL,
    CONSTRAINT AdminPK PRIMARY KEY (idAdmin),
    CONSTRAINT AdminAgentFK FOREIGN KEY (idAdmin) REFERENCES Agent (idAgent) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Department (
    idDepartment INTEGER NOT NULL,
    name TEXT NOT NULL,
    CONSTRAINT DepartmentPK PRIMARY KEY (idDepartment),
    CONSTRAINT DepartmentNameCK UNIQUE (name)
);

CREATE TABLE Priority (
    idPriority INTEGER NOT NULL,
    name TEXT NOT NULL,
    CONSTRAINT PriorityPK PRIMARY KEY (idPriority),
    CONSTRAINT PriorityNameCK UNIQUE (name)
);

CREATE TABLE Tag (
    idTag INTEGER NOT NULL,
    name TEXT NOT NULL,
    CONSTRAINT TagPK PRIMARY KEY (idTag),
    CONSTRAINT TagNameCK UNIQUE (name)
);

CREATE TABLE Status (
    idStatus INTEGER NOT NULL,
    name TEXT NOT NULL,
    CONSTRAINT StatusPK PRIMARY KEY (idStatus),
    CONSTRAINT StatusNameCK UNIQUE (name)
);

CREATE TABLE FAQ (
    idFAQ INTEGER NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    CONSTRAINT FAQPK PRIMARY KEY (idFAQ),
    CONSTRAINT FAQQuestionCK UNIQUE (question)
);

CREATE TABLE Ticket (
    idTicket INTEGER NOT NULL,
    idUser INTEGER NOT NULL,
    title TEXT NOT NULL,
    description TEXT NOT NULL,
    dateOpened DATE NOT NULL,
    dateClosed DATE,
    idAgent INTEGER,
    idDepartment INTEGER,
    idPriority INTEGER,
    idStatus INTEGER DEFAULT 1,
    idFAQ INTEGER,
    filename TEXT,
    CONSTRAINT TicketPK PRIMARY KEY (idTicket),
    CONSTRAINT TicketUserFK FOREIGN KEY (idUser) REFERENCES User (idUser) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT TicketAgentFK FOREIGN KEY (idAgent) REFERENCES Agent (idAgent) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT TicketDepartmentFK FOREIGN KEY (idDepartment) REFERENCES Department (idDepartment) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT TicketPriorityFK FOREIGN KEY (idPriority) REFERENCES Priority (idPriority) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT TicketStatusFK FOREIGN KEY (idStatus) REFERENCES Status (idStatus) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT TicketFAQFK FOREIGN KEY (idFAQ) REFERENCES FAQ (idFAQ) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT TicketCheckDateClosed CHECK (dateClosed IS NULL OR dateClosed >= dateOpened)
);

CREATE TABLE Change (
    idChange INTEGER NOT NULL,
    date DATE NOT NULL,
    description TEXT NOT NULL,
    idTicket INTEGER NOT NULL,
    CONSTRAINT ChangePK PRIMARY KEY (idChange),
    CONSTRAINT ChangeTicketFK FOREIGN KEY (idTicket) REFERENCES Ticket (idTicket) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE Message (
    idMessage INTEGER NOT NULL,
    date DATE NOT NULL,
    content TEXT NOT NULL,
    idTicket INTEGER NOT NULL,
    idUser INTEGER NOT NULL,
    CONSTRAINT MessagePK PRIMARY KEY (idMessage),
    CONSTRAINT MessageTicketFK FOREIGN KEY (idTicket) REFERENCES Ticket (idTicket) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT MessageUserFK FOREIGN KEY (idUser) REFERENCES User (idUser) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE AgentDepartment (
    idAgent INTEGER NOT NULL,
    idDepartment INTEGER NOT NULL,
    CONSTRAINT AgentDepartmentPK PRIMARY KEY (idAgent, idDepartment),
    CONSTRAINT AgentDepartmentAgentFK FOREIGN KEY (idAgent) REFERENCES Agent (idAgent) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT AgentDepartmentDepartmentFK FOREIGN KEY (idDepartment) REFERENCES Department (idDepartment) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE TicketTag (
    idTicket INTEGER NOT NULL,
    idTag INTEGER NOT NULL,
    CONSTRAINT TicketTagPK PRIMARY KEY (idTag, idTicket),
    CONSTRAINT TicketTagTicketFK FOREIGN KEY (idTicket) REFERENCES Ticket (idTicket) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT TicketTagTagFK FOREIGN KEY (idTag) REFERENCES Tag (idTag) ON UPDATE CASCADE ON DELETE CASCADE
);


/* TRIGGERS */

DROP TRIGGER IF EXISTS AdminAgent;
CREATE TRIGGER AdminAgent
    AFTER INSERT ON Admin
    WHEN NOT EXISTS (SELECT * FROM Agent WHERE idAgent = New.idAdmin)
BEGIN
    INSERT INTO Agent (idAgent) VALUES (New.idAdmin);
END;

DROP TRIGGER IF EXISTS TicketStatusAutoAssign;
CREATE TRIGGER TicketStatusAutoAssign
    AFTER UPDATE OF idAgent ON Ticket
    WHEN Old.idAgent IS NULL AND New.idAgent IS NOT NULL
BEGIN
    UPDATE Ticket SET idStatus = 2 WHERE idTicket = Old.idTicket;
END;

DROP TRIGGER IF EXISTS TicketStatusAutoFAQClose;
CREATE TRIGGER TicketStatusAutoFAQClose
    AFTER UPDATE OF idFAQ ON Ticket
    WHEN Old.idFAQ IS NULL AND New.idFAQ IS NOT NULL
BEGIN
    UPDATE Ticket SET idStatus = 3 WHERE idTicket = Old.idTicket;
END;

DROP TRIGGER IF EXISTS TicketStatusAutoDateClosed;
CREATE TRIGGER TicketStatusAutoDateClosed
    AFTER UPDATE OF idStatus ON Ticket
    WHEN New.idStatus = 3
BEGIN
    UPDATE Ticket SET dateClosed = date() WHERE idTicket = Old.idTicket;
END;

DROP TRIGGER IF EXISTS TicketTitle;
CREATE TRIGGER TicketTitle
    AFTER UPDATE OF title ON Ticket
    WHEN New.title <> Old.title
BEGIN
    INSERT INTO Change (date, description, idTicket) VALUES (date(), 'Title edited', New.idTicket);
END;

DROP TRIGGER IF EXISTS TicketDescription;
CREATE TRIGGER TicketDescription
    AFTER UPDATE OF description ON Ticket
    WHEN New.description <> Old.description
BEGIN
    INSERT INTO Change (date, description, idTicket) VALUES (date(), 'Description edited', New.idTicket);
END;

DROP TRIGGER IF EXISTS TicketAgent;
CREATE TRIGGER TicketAgent
    AFTER UPDATE OF idAgent ON Ticket
    WHEN New.idAgent <> Old.idAgent OR (New.idAgent IS NOT NULL AND Old.idAgent IS NULL)
BEGIN
    INSERT INTO Change (date, description, idTicket) VALUES (date(), 'Agent: ' || IFNULL((SELECT firstName || ' ' || lastName FROM Agent, User WHERE idAgent = idUser AND idAgent = Old.idAgent), 'None') || ' → ' || (SELECT firstName || ' ' || lastName FROM Agent, User WHERE idAgent = idUser AND idAgent = New.idAgent), New.idTicket);
END;

DROP TRIGGER IF EXISTS TicketDepartment;
CREATE TRIGGER TicketDepartment
    AFTER UPDATE OF idDepartment ON Ticket
    WHEN New.idDepartment <> Old.idDepartment OR (New.idDepartment IS NOT NULL AND Old.idDepartment IS NULL)
BEGIN
    INSERT INTO Change (date, description, idTicket) VALUES (date(), 'Department: ' || IFNULL((SELECT name FROM Department WHERE idDepartment = Old.idDepartment), 'None') || ' → ' || (SELECT name FROM Department WHERE idDepartment = New.idDepartment), New.idTicket);
END;

DROP TRIGGER IF EXISTS TicketPriority;
CREATE TRIGGER TicketPriority
    AFTER UPDATE OF idPriority ON Ticket
    WHEN New.idPriority <> Old.idPriority OR (New.idPriority IS NOT NULL AND Old.idPriority IS NULL)
BEGIN
    INSERT INTO Change (date, description, idTicket) VALUES (date(), 'Priority: ' || IFNULL((SELECT name FROM Priority WHERE idPriority = Old.idPriority), 'None') || ' → ' || (SELECT name FROM Priority WHERE idPriority = New.idPriority), New.idTicket);
END;

DROP TRIGGER IF EXISTS TicketStatus;
CREATE TRIGGER TicketStatus
    AFTER UPDATE OF idStatus ON Ticket
    WHEN New.idStatus <> Old.idStatus OR (New.idStatus IS NOT NULL AND Old.idStatus IS NULL)
BEGIN
    INSERT INTO Change (date, description, idTicket) VALUES (date(), 'Status: ' || IFNULL((SELECT name FROM Status WHERE idStatus = Old.idStatus), 'None') || ' → ' || (SELECT name FROM Status WHERE idStatus = New.idStatus), New.idTicket);
END;


/* INSERT */

INSERT INTO User VALUES(1, 'Joana', 'Marques', 'joanamarques', 'joanamarques@gmail.com', '$2y$12$VrJz37szQfuUJL6xqtW7AOTG8mo4P6MYS3ANzCfKjU1rptpMxT9fq', 'profile_default.png');
INSERT INTO User VALUES(2, 'Matilde', 'Simões', 'matildesimoes', 'matildesimoes@gmail.com', '$2y$12$GQxH53nyzhC52/rjmxFsIO0U3.zPyXIMI4RwUIcI15XUWLHQgBNy2', 'profile_default.png');
INSERT INTO User VALUES(3, 'Manel', 'Neto', 'manelneto', 'manelneto@gmail.com', '$2y$12$xTMVEOfoadee7NX5aV2.u.gSH5ZSHpenQOolQ.wT6vexkxS0bnhBi', 'profile_default.png');

INSERT INTO Agent VALUES(2);

INSERT INTO Admin VALUES(3);

INSERT INTO Department VALUES(1, 'Information Technology');
INSERT INTO Department VALUES(2, 'Human Resources');
INSERT INTO Department VALUES(3, 'Finances');
INSERT INTO Department VALUES(4, 'Marketing');
INSERT INTO Department VALUES(5, 'Logistics');
INSERT INTO Department VALUES(6, 'Legal');
INSERT INTO Department VALUES(7, 'Product Development');

INSERT INTO Priority VALUES(1, 'Low');
INSERT INTO Priority VALUES(2, 'Medium');
INSERT INTO Priority VALUES(3, 'High');
INSERT INTO Priority VALUES(4, 'Critical');

INSERT INTO Tag VALUES(1, 'bug');
INSERT INTO Tag VALUES(2, 'feature');
INSERT INTO Tag VALUES(3, 'urgent');
INSERT INTO Tag VALUES(4, 'performance');
INSERT INTO Tag VALUES(5, 'billing');
INSERT INTO Tag VALUES(6, 'issue');
INSERT INTO Tag VALUES(7, 'recurring');
INSERT INTO Tag VALUES(8, 'invalid');

INSERT INTO Status VALUES(1, 'Open');
INSERT INTO Status VALUES(2, 'Assigned');
INSERT INTO Status VALUES(3, 'Closed');

INSERT INTO FAQ VALUES(1, 'How long do I have to wait for an answer?', 'It depends on the question. On average, one week.');
INSERT INTO FAQ VALUES(2, 'Where can I see the current status of my ticket?', 'The client can check the ticket status in the Dashboard.');
INSERT INTO FAQ VALUES(3, 'Where can I submit a new ticket?', 'On the tickets section.');
INSERT INTO FAQ VALUES(4, 'Where can I change my email address?', 'On your profile section.');
INSERT INTO FAQ VALUES(5, 'Can I add attachments to my ticket?', 'Yes, you can add attachments while submitting a ticket or later by updating the ticket.');
INSERT INTO FAQ VALUES(6, 'How do I edit my submitted ticket?', 'You can edit your ticket by navigating to the ticket and selecting the edit option.');
INSERT INTO FAQ VALUES(7, 'Can I reopen a closed ticket?', 'No, once a ticket is closed, it cannot be reopened.');
INSERT INTO FAQ VALUES(8, 'What is the difference between departments?', 'Departments help to categorize the tickets. For example, the department "Information Technology" is used to indicate that the ticket is related to a problem with the system.');
INSERT INTO FAQ VALUES(9, 'Who can see my tickets?', 'Only the assigned agent and the administrators.');
INSERT INTO FAQ VALUES(10, 'What is the difference between priority levels?', 'Priority levels help to indicate the severity of the ticket. Critical priority tickets are treated with more urgency than low priority ones.');
INSERT INTO FAQ VALUES(11, 'What is the difference between tags?', 'Tags help to categorize the tickets. For example, the tag "bug" is used to indicate that the ticket is related to a bug in the system.');

INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(1, 1, 'Received a broken TV', 'The television I ordered from your site was delivered with a cracked screen. I need some replacement.', '2023-05-07');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(2, 2, 'Payment failed', 'The payment of my purchase failed. What can I do?', '2023-05-06');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(3, 1, 'Email address change', 'Where can I change my email address?', '2023-05-05');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(4, 3, 'Login Issue', 'I cannot log into my account despite using the correct password.', '2023-05-09');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(5, 1, 'Incorrect order received', 'I received an item that I did not order.', '2023-05-09');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(6, 2, 'Order not yet delivered', 'My order was supposed to arrive last week but it has not arrived yet.', '2023-05-10');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(7, 1, 'Product return', 'I would like to return a product I purchased, but I am unsure of the process.', '2023-05-11');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(8, 3, 'Website Error', 'I am receiving a 404 error when trying to access my cart.', '2023-05-11');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(9, 3, 'Promotion code not working', 'The promotion code I am trying to use is not being accepted at checkout.', '2023-05-12');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(10, 1, 'Product Query', 'Does the product come with a warranty?', '2023-05-13');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(11, 1, 'Login Issue', 'I cannot log into my account despite using the correct password.', '2023-05-09');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(12, 1, 'Damaged Package', 'The package was damaged upon arrival, and I am concerned about the product inside.', '2023-05-15');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(13, 1, 'Payment Refund', 'I would like to request a refund for my last purchase.', '2023-05-17');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(14, 1, 'Technical Glitch', 'I am experiencing a technical issue with the website. It freezes every time I try to check out.', '2023-05-19');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(15, 1, 'Product Unavailability', 'The product I want to purchase is out of stock. When will it be available?', '2023-05-20');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(16, 1, 'Order Cancellation', 'I accidentally placed an order. I need to cancel it.', '2023-05-21');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(17, 1, 'Billing Discrepancy', 'I was charged twice for my last purchase. Please resolve.', '2023-05-22');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(18, 2, 'Website Navigation', 'I am having trouble finding a specific section on website. Can you assist?', '2023-05-21');
INSERT INTO Ticket(idTicket, idUser, title, description, dateOpened) VALUES(19, 1, 'Account Deletion', 'I want to delete my account, but I am unable to find the option.', '2023-05-01');

UPDATE Ticket SET idAgent = 2, idDepartment = 3, idPriority = 3 WHERE idTicket = 2;
UPDATE Ticket SET idStatus = 3 WHERE idTicket = 2;

INSERT INTO Message VALUES(1, '2023-05-15', 'Forget it. I fixed the screen myself!', 1, 1);
INSERT INTO Message VALUES(2, '2023-05-07', 'What is the number of your purchase?', 2, 2);
INSERT INTO Message VALUES(3, '2023-05-08', 'Purchase Number 123', 2, 2);

INSERT INTO AgentDepartment VALUES(2, 2);
INSERT INTO AgentDepartment VALUES(2, 4);

INSERT INTO TicketTag VALUES(1, 4);
INSERT INTO TicketTag VALUES(2, 4);
INSERT INTO TicketTag VALUES(3, 1);
INSERT INTO TicketTag VALUES(3, 3);


/* TRIGGERS */

DROP TRIGGER IF EXISTS TicketTagInsert;
CREATE TRIGGER TicketTagInsert
    AFTER INSERT ON TicketTag
BEGIN
    INSERT INTO Change (date, description, idTicket) VALUES (date(), 'Tag: + ' || (SELECT name FROM Tag NATURAL JOIN TicketTag WHERE idTicket = New.idTicket AND idTag = New.idTag), New.idTicket);
END;

DROP TRIGGER IF EXISTS TicketTagDelete;
CREATE TRIGGER TicketTagDelete
    BEFORE DELETE ON TicketTag
BEGIN
    INSERT INTO Change (date, description, idTicket) VALUES (date(), 'Tag: - ' || (SELECT name FROM Tag NATURAL JOIN TicketTag WHERE idTicket = Old.idTicket AND idTag = Old.idTag), Old.idTicket);
END;
