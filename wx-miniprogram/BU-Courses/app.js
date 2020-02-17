/**
 * KRUNK.CN - BU课程表
 * Made for Brock University 2020 SCS
 * Dev Website: https://krunk.cn
 * Date: 2020/02
 */

//app.js
App({

  showErrModal: function (err_msg) {
    wx.showModal({
      content: err_msg,
      showCancel: false
    });
  },

  onLaunch: function () {
    // 展示本地存储能力
    //var logs = wx.getStorageSync('logs') || []
    //logs.unshift(Date.now())
    //wx.setStorageSync('logs', logs)

    // 登录
    wx.login({
      success: res => {
        // 发送 res.code 到后台换取 openId, sessionKey, unionId
        //console.log(res.code);
        wx.request({
          url: this.globalData.base_url + "openid.php",
          method: "post",
          header: { "content-type": "application/x-www-form-urlencoded" },
          data: {
            token: this.globalData.kapi_token,
            code: res.code
          },
          success: function (resp) {
            console.log(resp);
            var resp_dict = resp.data;
            if (resp_dict.code == 1) {
              wx.setStorage({
                key: 'openid',
                data: resp_dict.openid,
              });
            } else {
              console.log(resp.data);
              console.log('Token错误');
              wx.showToast({
                title: '离线模式',
              });
            }
          }
        })

      }
    })
    // 获取用户信息
    wx.getSetting({
      success: res => {
        if (res.authSetting['scope.userInfo']) {
          // 已经授权，可以直接调用 getUserInfo 获取头像昵称，不会弹框
          wx.getUserInfo({
            success: res => {
              // 可以将 res 发送给后台解码出 unionId
              this.globalData.userInfo = res.userInfo

              // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
              // 所以此处加入 callback 以防止这种情况
              if (this.userInfoReadyCallback) {
                this.userInfoReadyCallback(res)
              }
            }
          })
        }
      }
    })
  },
  globalData: {
    base_url: '',
    kapi_token: '',
    userInfo: null
  }
})