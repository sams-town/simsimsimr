const express = require('express');
var cors = require('cors');
const app = express();
app.use(cors());

const server = app.listen(3001, function() {
    console.log('server running on port 3001');
});
const io = require('socket.io')(server,{
    allowEIO3: true,
    cors: {
        origin: "http://localhost:8080",
        methods: ["GET", "POST"],
        allowedHeaders: ["my-custom-header"],
        credentials: true
    }
  });
io.on('connection', function(socket) {
    console.log(socket.id)
    socket.on('SEND_ANJUNGAN', function(data) {
        io.emit('GET_LOKET', data)
    });
    socket.on('SEND_LOKET', function(data) {
        io.emit('GET_DISPLAY', data)
    });
    socket.on('SEND_ANJUNGAN_CHECKIN', function(data) {
        io.emit('GET_WORKORDER', data)
    });
    socket.on('SEND_WORKORDER', function(data) {
        io.emit('GET_DISPLAY_POLI', data)
    });
    socket.on('SEND_PRS', function(data) {
        io.emit('GET_DISPLAY_PRS', data)
    });
    socket.on('SEND_LAB', function(data) {
        io.emit('GET_DISPLAY_LAB', data)
    });
    socket.on('SEND_RAD', function(data) {
        io.emit('GET_DISPLAY_RAD', data)
    });
    socket.on('SEND_MESSAGE', function(data) {
        io.emit('MESSAGE', data)
    });
});
