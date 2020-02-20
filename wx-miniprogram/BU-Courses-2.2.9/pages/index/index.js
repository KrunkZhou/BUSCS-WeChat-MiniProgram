/**
 * KRUNK.CN - BU课程表
 * Made for Brock University 2020 SCS
 * Dev Website: https://krunk.cn
 * Date: 2020/02
 */

//index.js
//获取应用实例
const app = getApp()
var util = require("../../utils/util.js")

Page({
  data: {
    userInfo: {},
    hasUserInfo: false,
    course_index: [],
    week_list:['一','二','三','四','五'],
    today_week:'',
    curDay:'',
    courseColors: {'LEC':'#0373a3'},
    time_array: [7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24],
    tab_line: [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]
  },
  showCardView: function (event) {
    var data = event.currentTarget.dataset.name;
    var datainfo = event.currentTarget.dataset.info;
    wx.showModal({
      title: data,
      content: datainfo,
      showCancel: false
      /*success(res) {
        if (res.confirm) {
          console.log('用户点击确定')
        } else if (res.cancel) {
          console.log('用户点击取消')
        }
      }*/
    })
  },
  onLoad: function () {
    var date = new Date();
    let year = date.getFullYear();
    let month = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
    let day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    let weekdate = util.getWeekText(year, month, day);
    this.setData({
      today_week: weekdate,
      curDay: day
    })

    try{
      if (wx.getStorageSync("login") == 1) {
        this.setData({
          motto: wx.getStorageSync("username"),
          course_index: JSON.parse(wx.getStorageSync("course_index")),
          today_week: weekdate,
          curMonth: day
        })
      }
    }catch(e){}
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        //hasUserInfo: true
      })
    } else if (this.data.canIUse){
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          //hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.setData({
            userInfo: res.userInfo,
            //hasUserInfo: true
          })
        }
      })
    }
  },
  getUserInfo: function(e) {
    console.log(e)
    app.globalData.userInfo = e.detail.userInfo
    this.setData({
      userInfo: e.detail.userInfo,
      //hasUserInfo: true
    })
  },

  onShow: function () {
    if (wx.getStorageSync("login") == 1) {
      try{
        this.setData({
          hasUserInfo: true,
          course_index: JSON.parse(wx.getStorageSync("course_index"))
        })
      }catch(e){}
    }else{
      this.setData({
        hasUserInfo: false,
        course_index: []
      })
    }
  },
  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
    if (wx.getStorageSync("login") == 1) {
      try{
        this.setData({
          hasUserInfo: true,
          course_index: JSON.parse(wx.getStorageSync("course_index"))
        })
      }catch(e){}
    }
    var date = new Date();
    let year = date.getFullYear();
    let month = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
    let day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    let weekdate = util.getWeek(year, month, day);
    this.setData({
      today_week: weekdate
    })
    wx.stopPullDownRefresh();
  },
  onShareAppMessage: function () {
    var that = this;
    try{
      return {
        title: app.globalData.userInfo.nickName + '与您共享ta的课表 - Brock 课表查询神器',
        path: 'pages/share/share?id=' + wx.getStorageSync("username") + '&sharetoken=' + getApp().globalData.share_token,
        success: function (res) {
          // 转发成功
        },
        fail: function (res) {
          // 转发失败
        }
      }
    }catch(e){
      return {
        title: 'Brock University 课表查询神器',
        path: 'pages/index/index',
        success: function (res) {
          // 转发成功
        },
        fail: function (res) {
          // 转发失败
        }
      }
    }
  },
})
