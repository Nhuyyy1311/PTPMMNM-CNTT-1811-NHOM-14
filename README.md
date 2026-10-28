# PTPMMNN-CNTT-1811-NHOM-14
# Hệ thống Xếp Thời Khóa Biểu (Smart Timetable)

Đề tài 18: Xây dựng hệ thống xếp thời khóa biểu theo ràng buộc giảng viên, phòng học và lớp học.

## 👥 Thành viên nhóm
- Đề tài thuộc học phần Phát triển phần mềm mã nguồn mở (PTPMMNN).

## 🛠️ Công nghệ sử dụng
- **Ngôn ngữ:** PHP (PDO)
- **Cơ sở dữ liệu:** MySQL (phpMyAdmin - CSDL: `xep_thoi_khoa_bieu`)
- **Giao diện:** HTML5, CSS3 (Responsive Dark Mode)
- **Môi trường chạy:** XAMPP Server

## 🚀 Hướng dẫn cài đặt và chạy thử
1. Khởi động dịch vụ **Apache** và **MySQL** trên **XAMPP**.
2. Truy cập phpMyAdmin (`http://localhost/phpmyadmin/`), tạo cơ sở dữ liệu mới tên là `xep_thoi_khoa_bieu`.
3. Import file cơ sở dữ liệu mẫu (`database/xepthoikhoabieu.sql`) vào CSDL vừa tạo.
4. Clone hoặc copy toàn bộ mã nguồn vào thư mục gốc của XAMPP: `C:\xampp\htdocs\XepThoiKhoaBieu\`.
5. Mở trình duyệt web và truy cập đường dẫn: 
   `http://localhost/XepThoiKhoaBieu/`

## ✨ Các chức năng chính của hệ thống
- **Quản lý danh mục đào tạo:** Thêm, sửa, xóa và quản lý thông tin Giảng viên (có số điện thoại), Phòng học, Lớp học và Khung giờ học.
- **Kiểm tra trùng lặp thông minh:** Tự động phát hiện và cảnh báo lỗi khi nhập trùng mã hoặc xung đột phòng học, khung giờ.
- **Lập lịch thời khóa biểu:** Phân công chi tiết buổi học, phòng học, giảng viên theo từng thứ trong tuần và ca học.
- **Giao diện tối (Dark Theme):** Thiết kế hiện đại, trực quan, thao tác mượt mà.