/**
 * KRUNK.CN - BU课程表
 * Made for Brock University 2020 SCS
 * Dev Website: https://krunk.cn
 * Date: 2020/02
 */

// pages/list/list.js
var util = require("../../utils/util.js")

Page({

  /**
   * 页面的初始数据
   */
  data: {
    hasUserInfo: false,
    course_list: [],
    today_week: "1"
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var date = new Date();
    let year = date.getFullYear();
    let month = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
    let day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    let weekdate = util.getWeek(year, month, day);
    this.setData({
      today_week: weekdate
    })
    if (wx.getStorageSync("login") == 1) {
      try{
        this.setData({
          hasUserInfo: true,
          course_list: JSON.parse(wx.getStorageSync("course_list"))
        })
      }catch(e){}
    }
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {

  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    if (wx.getStorageSync("login")==1){
      try{
        this.setData({
          hasUserInfo: true,
          course_list: JSON.parse(wx.getStorageSync("course_list"))
        })
      }catch(e){}
    }else {
      this.setData({
        hasUserInfo: false,
        course_list: []
      })
    }
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
    if (wx.getStorageSync("login") == 1) {
      try{
        this.setData({
          hasUserInfo: true,
          course_list: JSON.parse(wx.getStorageSync("course_list"))
        })
      }catch(e){}
    }
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {

  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
    if (wx.getStorageSync("login") == 1) {
      try{
        this.setData({
          hasUserInfo: true,
          course_list: JSON.parse(wx.getStorageSync("course_list"))
        })
      }catch(e){}
    }
    wx.stopPullDownRefresh();
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {

  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
    var that = this;
    try {
      return {
        title: app.globalData.userInfo.nickName + '与您共享ta的课表 - Brock课表查询神器',
        path: 'pages/share/share?id=' + wx.getStorageSync("username"),
        success: function (res) {
          // 转发成功
        },
        fail: function (res) {
          // 转发失败
        }
      }
    } catch (e) {
      return {
        title: 'Brock University课表查询神器',
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