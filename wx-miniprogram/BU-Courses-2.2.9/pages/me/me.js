/**
 * KRUNK.CN - BU课程表
 * Made for Brock University 2020 SCS
 * Dev Website: https://krunk.cn
 * Date: 2020/02
 */

//index.js
//获取应用实例
const app = getApp()

Page({
  data: {
    motto: '',
    userInfo: {},
    hasUserInfo: false,
    canIUse: wx.canIUse('button.open-type.getUserInfo')
  },
  //事件处理函数
  bindViewTap: function() {
    //wx.navigateTo({
    //  url: '../logs/logs'
    //})
  },
  clearStorage: function () {
    var that=this;
    wx.showToast({
      title: '正在加载',
      icon: 'loading'
    });

    wx.request({
      url: app.globalData.base_url + "req-del.php",
      method: "post",
      header: { "content-type": "application/x-www-form-urlencoded" },
      data: {
        token: app.globalData.kapi_token,
        un: wx.getStorageSync("username")
      },
      success: function (resp) {
        console.log(resp);
        var resp_dict = resp.data;
        if (resp_dict.code == 1) {
          wx.hideToast();
          wx.showToast({
            title: '删除成功',
          });
        }else if (resp_dict.code == 2) {
          wx.hideToast();
          console.log(resp.data);
          wx.showToast({
            title: '数据已被删除',
          });
        }  else {
          wx.hideToast();
          console.log(resp.data);
          wx.showToast({
            title: '删除失败',
          });
        }
      }
    })

    try {
      wx.clearStorageSync();
    } catch (e) {
      wx.hideToast();
      wx.showToast({
        title: '本地数据删除失败',
      });
    }
  },
  onLoad: function () {
    this.setData({
      motto: wx.getStorageSync("username")
    })
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        hasUserInfo: true
      })
    } else if (this.data.canIUse){
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.setData({
            userInfo: res.userInfo,
            hasUserInfo: true
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
      hasUserInfo: true
    })
  },
  
  onShow: function () {
    this.setData({
      motto: wx.getStorageSync("username")
    })
  },
})
