 
/**
 * 获取七牛token
 */
// var getQiNiuToken = (that) => {
//   that.axios.post("/Qiniu/getToken", {
//     token: that.token,
//   }, {
//     emulateJSON: true
//   }).then(
//     function (res) {
//       // 处理成功的结果
//       that.postData = {
//         token: res.upToken,
//       }
//       that.domain = res.domain;
//     });
// }

/**
 * 获取省市区
 */
var getCity = (that) => {
  that.axios.post("/Public_all/all_province", {}, {
    emulateJSON: true
  }).then(res => {
    var data = res;
    if (data) {
      var arr = [];
      data.forEach((ele, i) => {

        arr.push({
          label: ele.province_name,
          value: ele.id,
          children: []
        })

        if (ele.city_list) {
          ele.city_list.forEach((item, index) => {
            arr[i].children.push({
              label: item.city_name,
              value: item.id,
            })
          })
        }

      })
      that.cityData = arr;
      sessionStorage.setItem('city_data', JSON.stringify(arr))
    }
  })
}

/**
 * 时间转换
 */
var formatTime = (date, type) => {
  var y = date.getFullYear();
  var m = date.getMonth() + 1;
  m = m < 10 ? ('0' + m) : m;
  var d = date.getDate();
  d = d < 10 ? ('0' + d) : d;
  var h = date.getHours();
  h = h < 10 ? ('0' + h) : h;
  var minute = date.getMinutes();
  minute = minute < 10 ? ('0' + minute) : minute;
  if (type) {
    return y + '-' + m + '-' + d;
  } else {
    return y + '-' + m + '-' + d + ' ' + h + ':' + minute;
  }
}

/**
 * 获取年份 
 */
var get_year = (that) => {
  that.axios.post("/magazine/get_data", {}, {
    emulateJSON: true
  }).then(res => {
    var data = res;
    if (data) {
      that.year_data = data;
    }
  })
}

// 记录当前页
var save_page = (that) => {
  sessionStorage.setItem('curr_page', that.page)
}


export default {
  formatTime,
  // getQiNiuToken,
  getCity,
  get_year,
  save_page
}
