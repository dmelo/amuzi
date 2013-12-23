alter table user modify privacy enum('public', 'private') not null default 'private';
