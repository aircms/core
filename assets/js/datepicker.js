$(document).ready(() => {
  const aTPS = {
    locale: {
      days: [
        locale('Sunday'),
        locale('Monday'),
        locale('Tuesday'),
        locale('Wednesday'),
        locale('Thursday'),
        locale('Friday'),
        locale('Saturday'),
      ],
      daysShort: [
        locale('Sun'),
        locale('Mon'),
        locale('Tue'),
        locale('Wed'),
        locale('Thu'),
        locale('Fri'),
        locale('Sat')
      ],
      daysMin: [
        locale('Su'),
        locale('Mo'),
        locale('Tu'),
        locale('We'),
        locale('Th'),
        locale('Fr'),
        locale('Sa'),
      ],
      months: [
        locale('January'),
        locale('February'),
        locale('March'),
        locale('April'),
        locale('May'),
        locale('June'),
        locale('July'),
        locale('August'),
        locale('September'),
        locale('October'),
        locale('November'),
        locale('December'),
      ],
      monthsShort: [
        locale('Jan'),
        locale('Feb'),
        locale('Mar'),
        locale('Apr'),
        locale('May'),
        locale('Jun'),
        locale('Jul'),
        locale('Aug'),
        locale('Sep'),
        locale('Oct'),
        locale('Nov'),
        locale('Dec'),
      ],
      today: locale('Today'),
      clear: locale('Clear'),
      dateFormat: 'MM/dd/yyyy',
      timeFormat: 'HH:mm',
      firstDay: 1
    },
    buttons: [locale('clear')]
  };

  wait.on('[data-admin-datepicker]', (dP) => new AirDatepicker(dP, aTPS));
  wait.on('[data-admin-timepicker]', (tP) => new AirDatepicker(tP, {...aTPS, timepicker: true, onlyTimepicker: true}));
  wait.on('[data-admin-datetimepicker]', (dTP) => new AirDatepicker(dTP, {...aTPS, timepicker: true}));
});