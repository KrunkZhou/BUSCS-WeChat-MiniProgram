/**
 * KRUNK.CN - BU课程表
 * Made for Brock University 2020 SCS
 * Dev Website: https://krunk.cn
 * Date: 2020/02
 */

// pages/user/user.js
const app = getApp()

Page({

  /**
   * 页面的初始数据
   */
  data: {
    username: '',
    password: '',
    username_display: ''
  },

  inputUsername: function (e) {
    this.setData({
      username: e.detail.value
    });
  },

  inputPassword: function (e) {
    this.setData({
      password: e.detail.value
    });
  },

  clickLogin: function (e) {
    var that = this;

    var username = that.data.username;
    if (username == null || username == undefined || username == '') {
      getApp().showErrModal('账号不能为空');
      return;
    }

    var password = that.data.password;
    if (password == null || password == undefined || password == '') {
      getApp().showErrModal('密码不能为空');
      return;
    }

    wx.showToast({
      title: '正在登录',
      icon: 'loading'
    });

    wx.request({
      url: getApp().globalData.base_url + "login.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        un: encodeURI(that.data.username),
        pw: that.data.password,
        ui: JSON.stringify(getApp().globalData.userInfo),
        token: getApp().globalData.kapi_token,
        openid: wx.getStorageSync("openid")
      },
      success: function (resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.code == 1) {
          wx.setStorage({
            key: 'login',
            data: 1,
          });
          wx.setStorage({
            key: 'username',
            data: that.data.username,
          })
          wx.setStorage({
            key: 'course_index',
            data: resp_dict.course_index,
          })
          wx.setStorage({
            key: 'course_list',
            data: resp_dict.course_list,
          })
          wx.switchTab({
            url: "../index/index"
          });
          wx.hideToast();
          wx.showToast({
            title: '登录成功',
          });
        } else if (resp_dict.code == 0){
          wx.hideToast();
          console.log(resp.data);
          getApp().showErrModal('用户名或密码错误');
        }else{
          //wx.hideToast();
          console.log(resp.data);
          //getApp().showErrModal('无法连接到服务器');
          wx.showToast({
            title: '备用服务器',
            icon: 'loading'
          });
          
          //连接备用服务器
          wx.request({
            url: getApp().globalData.backup_url + "login.php",
            method: "post",
            header: { "content-type": "application/x-www-form-urlencoded" },
            data: {
              un: encodeURI(that.data.username),
              pw: that.data.password,
              ui: JSON.stringify(getApp().globalData.userInfo),
              token: getApp().globalData.kapi_token,
              openid: wx.getStorageSync("openid")
            },
            success: function (resp) {
              console.log(resp);
              var resp_dict = resp.data;
              if (resp_dict.code == 1) {
                wx.setStorage({
                  key: 'login',
                  data: 1,
                });
                wx.setStorage({
                  key: 'username',
                  data: that.data.username,
                })
                wx.setStorage({
                  key: 'course_index',
                  data: resp_dict.course_index,
                })
                wx.setStorage({
                  key: 'course_list',
                  data: resp_dict.course_list,
                })
                wx.switchTab({
                  url: "../index/index"
                });
                wx.hideToast();
                wx.showToast({
                  title: '登录成功',
                });
              } else if (resp_dict.code == 0) {
                wx.hideToast();
                console.log(resp.data);
                getApp().showErrModal('用户名或密码错误');
              } else {
                wx.hideToast();
                console.log(resp.data);
                getApp().showErrModal('无法连接到备用服务器');
              }
            }
          })


        }
      }
    })
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {

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
    this.setData({
      username_display: wx.getStorageSync("username"),
      username: wx.getStorageSync("username")
    })
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {

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

  }
})