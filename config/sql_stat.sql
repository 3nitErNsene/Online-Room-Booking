create database rm_booking;
use rm_booking;

create table staff (
	staff_id char(30) Not Null,
	login_name char(100) Not Null,
	pwd char(100) Not Null,
	dept char(20) Not Null,
	email char(50) Not Null,
	post char(20) Not Null,

	constraint staff_id_pk primary key (staff_id)
);

create table room (
	room char(30) Not Null,
	status char(5) Not Null,

	constraint room_pk primary key (room)
);

create table booking (
	id numeric(50) Not Null,
	room char(30) Not Null,
	bk_day date Not Null,
	start_t char(5) Not Null,
	end_t char(5) Not Null,
	staff_id char(30) Not Null,
	title char(100) Not Null,

	constraint id_pk primary key (id),
	constraint booking_staff_fk foreign key (staff_id) references staff(staff_id),
	constraint booking_room_fk foreign key (room) references room(room)
);

Insert into staff values ('s1', 'regenehung', 'bXlwYXNzd29yZA==', 'IT', 'regene@abc.hk', 'admin');
Insert into staff values ('s2', 'EllenTong', 'ZWxsZW5QYXNz', 'Marketing', 'ellenTong@abc.hk', 'staff');

Insert into room values('M201', 'O');
Insert into room values('M202', 'O');
Insert into room values('M301', 'O');
Insert into room values('M302', 'C');
Insert into room values('M303', 'O');

Insert into booking values(1, 'M201', '2018-08-20', '09:30', '10:30', 's1', 'Review for A project');