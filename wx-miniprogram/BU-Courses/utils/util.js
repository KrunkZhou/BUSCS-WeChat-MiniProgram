/**
 * KRUNK.CN - BU课程表
 * Made for Brock University 2020 SCS
 * Dev Website: https://krunk.cn
 * Date: 2020/02
 */

const formatTime = date => {
  const year = date.getFullYear()
  const month = date.getMonth() + 1
  const day = date.getDate()
  const hour = date.getHours()
  const minute = date.getMinutes()
  const second = date.getSeconds()

  return [year, month, day].map(formatNumber).join('/') + ' ' + [hour, minute, second].map(formatNumber).join(':')
}

const formatNumber = n => {
  n = n.toString()
  return n[1] ? n : '0' + n
}

function getWeek(y, m, d) {
  const startWeek = 1; if (m < 3) {
    m += 12;
    --y;
  } return (d + startWeek + 2 * m + Math.floor(3 * (m + 1) / 5) + y + Math.floor(y / 4) - Math.floor(y / 100) + Math.floor(y / 400)) % 7;
} 

function getWeekText(y, m, d) {
  const aWeekTxt = ['日', '一', '二', '三', '四', '五', '六'];
  return aWeekTxt[getWeek(y, m, d)];
}

module.exports = {
  formatTime: formatTime,
  getWeek: getWeek,
  getWeekText: getWeekText,
}

