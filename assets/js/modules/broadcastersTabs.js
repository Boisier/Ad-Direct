function setBroadcasterTab (tabID) {
  console.log(tabID)

  if (tabID === currentBroadcasterTab) {
    return;
  }

  $('.broadcaster').stop()

  var oldTab = currentBroadcasterTab
  currentBroadcasterTab = tabID

  $('.' + oldTab + '-tab').removeClass('active')
  $('.' + currentBroadcasterTab + '-tab').addClass('active')

  $('.' + oldTab + '-broadcaster').fadeOut(250, function () {
    $('.' + currentBroadcasterTab + '-broadcaster').fadeIn(250)
  })
}
