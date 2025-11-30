(function () {
  "use strict";

function getTimeRemaining(endtime) {
    const total = Date.parse(endtime) - Date.parse(new Date());
    const seconds = Math.floor((total / 1000) % 60);
    const minutes = Math.floor((total / 1000 / 60) % 60);
    const hours = Math.floor((total / (1000 * 60 * 60)) % 24);
    const days = Math.floor(total / (1000 * 60 * 60 * 24));
  
    return {
      total,
      days,
      hours,
      minutes,
      seconds
    };
  }
  
  function initializeClock(elem, endtime) {
    const clock =  document.querySelector(elem)
    const daysSpan = clock.querySelector('[data-days]')
    const hoursSpan = clock.querySelector('[data-hours]')
    const minutesSpan = clock.querySelector('[data-minutes]')
    const secondsSpan = clock.querySelector('[data-seconds]')
  
    function updateClock() {
        const t = getTimeRemaining(endtime)

        daysSpan.innerHTML = t.days
        hoursSpan.innerHTML = ('0' + t.hours).slice(-2)
        minutesSpan.innerHTML = ('0' + t.minutes).slice(-2)
        secondsSpan.innerHTML = ('0' + t.seconds).slice(-2)

        // Removido: contador continua mesmo após zero
    }
  
    updateClock()
    const timeinterval = setInterval(updateClock, 1000)
  }
  
  let time = document.querySelector('.countdown').getAttribute('data-date')
  if (time == undefined) {
    // Definir uma data futura de pelo menos 1 dia e 4 horas
    const now = new Date()
    now.setDate(now.getDate() + 1) // +1 dia
    now.setHours(now.getHours() + 4) // +4 horas
    time = now.toISOString()
  }
  const deadline = new Date(time)
  initializeClock('.countdown', deadline)

})()