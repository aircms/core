$(document).ready(() => {
  const aTPS = {
    locale: {
      days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
      daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
      daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
      months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      today: 'Today',
      clear: 'Clear',
      dateFormat: 'MM/dd/yyyy',
      timeFormat: 'HH:mm',
      firstDay: 1
    },
    buttons: ['clear']
  };

  wait.on('[data-admin-datepicker]', (dP) => new AirDatepicker(dP, aTPS));
  wait.on('[data-admin-timepicker]', (tP) => new AirDatepicker(tP, {...aTPS, timepicker: true, onlyTimepicker: true}));
  wait.on('[data-admin-datetimepicker]', (dTP) => new AirDatepicker(dTP, {...aTPS, timepicker: true}));
});