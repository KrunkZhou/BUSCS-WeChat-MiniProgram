/**
 * KRUNK.CN - BU课程表
 * Made for Brock University 2020 SCS
 * Dev Website: https://krunk.cn
 * Date: 2020/02
 */

const app = getApp()
var util = require("../../utils/util.js")

Page({

  data: {
    userInfo: {},
    hasUserInfo: false,
    course_share: [],
    week_list: ['一', '二', '三', '四', '五'],
    today_week: '',
    curMonth: '',
    courseColors: { 'LEC': '#0373a3', 'TUT': '#CCFFCC' },
    time_array: [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
    tab_line: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18]
  },

  goindex: function () {
    wx.switchTab({
     url: '../index/index'
    })
  },

  showCardView: function (event) {
    var data = event.currentTarget.dataset.name;
    var datainfo = event.currentTarget.dataset.info;
    wx.showModal({
      title: data,
      content: datainfo,
      showCancel: false
    })
  },
  onLoad: function (options) {
    var that = this;

    var date = new Date();
    let year = date.getFullYear();
    let month = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
    let day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    let weekdate = util.getWeekText(year, month, day);
    this.setData({
      today_week: weekdate,
      curMonth: day
    })

    var share_id = options.id;
    var share_token = options.sharetoken;

    if (share_token == getApp().globalData.share_token){

      wx.showToast({
        title: '正在加载',
        icon: 'loading'
      });

      wx.request({
        url: getApp().globalData.base_url + "share.php",
        method: "post",
        header: { "content-type": "application/x-www-form-urlencoded" },
        data: {
          share_id: share_id,
          token: getApp().globalData.kapi_token,
          openid: wx.getStorageSync("openid"),
          sharetoken: share_token
        },
        success: function (resp) {
          console.log(resp);
          var resp_dict = resp.data;
          if (resp_dict.code == 1) {
            wx.hideToast();
            that.setData({
              hasUserInfo: true,
              course_share: JSON.parse(resp_dict.course_share)
            })
          } else if (resp_dict.code == 2) {
            console.log(resp.data);
            wx.hideToast();
            getApp().showErrModal('Token 验证失败 (1)');
          } else {
            console.log(resp.data);
            wx.hideToast();
            getApp().showErrModal('未找到数据');
          }
        }
      })

    }else{
      getApp().showErrModal('Token 验证失败 (0)');
      console.log('Share Token Error');
    }

  },

  getUserInfo: function (e) {
    console.log(e)
    app.globalData.userInfo = e.detail.userInfo
    this.setData({
      userInfo: e.detail.userInfo,
      //hasUserInfo: true
    })
  },

  onShow: function () {

  },
})
