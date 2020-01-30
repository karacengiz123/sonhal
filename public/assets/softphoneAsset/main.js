const electron = require('electron')
const  { app, BrowserWindow } = electron

let win

function createWindow () {
  // Tarayıcı penceresini oluştur.
  const {width, height} = electron.screen.getPrimaryDisplay().workAreaSize
  win = new BrowserWindow({ width: width, height: 200,frame: false })
  win.setPosition(0,height-180,true)
  // ve uygulamanın index.html'sini yükle.
  win.loadFile('screen.html')

  // Open the DevTools.
//  win.webContents.openDevTools()

  // Pencere kapatıldığında ortaya çıkar.
  win.on('closed', () => {
    //Pencere nesnesini referans dışı bırakın,
    // uygulamanız çoklu pencereleri destekliyorsa genellikle pencereleri
    // bir dizide saklarsınız, bu, ilgili öğeyi silmeniz gereken zamandır.
    win = null
  })
}
// Bu yöntem, Electron başlatmayı tamamladığında
// ve tarayıcı pencereleri oluşturmaya hazır olduğunda çağrılır.
// Bazı API'ler sadece bu olayın gerçekleşmesinin ardından kullanılabilir.
app.on('ready', createWindow)

// Bütün pencereler kapatıldığında çıkış yap.
app.on('window-all-closed', () => {
  // MacOS'de kullanıcı CMD + Q ile çıkana dek uygulamaların ve menü barlarının
  // aktif kalmaya devam etmesi normaldir.
  if (process.platform !== 'darwin') {
    app.quit()
  }
})

app.on('activate', () => {
  // MacOS'de dock'a tıklandıktan sonra eğer başka pencere yoksa
  // yeni pencere açılması normaldir.
  if (win === null) {
    createWindow()
  }
})
// Bu dosyada, uygulamanızın özel ana işleminin geri kalan bölümünü ekleyebilirsiniz
// Kod. Ayrıca bunları ayrı dosyalara koyabilir ve buradan isteyebilirsiniz.