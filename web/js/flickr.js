var flickr = {
    req: null,
    api_key: '380a13980a55296f2bd936a9bb3f4dfe',
    url: 'https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key={{api_key}}&tags={{tags}}&sort=relevance&format=json&nojsoncallback=1',
    url_img: 'https://farm{{farm-id}}.staticflickr.com/{{server-id}}/{{id}}_{{secret}}_z.jpg',
    getFlickrUrl: function() {
        return this.url.replace('{{api_key}}', this.api_key).replace('{{tags}}', tags.trim().split(' ').join(''));
    },
    getImgUrl: function(data) {
        return this.url_img.replace('{{farm-id}}', data.farm).replace('{{server-id}}', data.server).replace('{{id}}', data.id).replace('{{secret}}', data.secret);
    }
};

var container = document.querySelector('#flickr');
flickr.req = requestAsync(flickr.getFlickrUrl(), function(status, json) {
    if (status == 200) {
        json = JSON.parse(json);
        for (var i = 0; i < 10; i++) {
            var photo = json.photos.photo[i];
            var img = flickr.getImgUrl(photo);
            var imgName = photo.title;
            var imgUrl = 'https://www.flickr.com/photos/' + photo.owner + '/' + photo.id;
            container.appendChild(createFlickrElement(imgName, img, imgUrl));
        }
    }
});

function createFlickrElement(imgName, img, imgUrl) {
    var liElement = document.createElement('li');
    var figElement = document.createElement('figure');
    var aElement = document.createElement('a');
    aElement.href = imgUrl;
    aElement.target = '_blank';
    aElement.title = imgName;

    var imgContainerElement = document.createElement('div');
    imgContainerElement.className = 'imgContainer';
    var imgElement = document.createElement('img');
    imgElement.src = img;
    imgElement.alt = imgName;
    imgContainerElement.appendChild(imgElement);
    aElement.appendChild(imgContainerElement);

    var infosElement = document.createElement('figcaption');
    infosElement.textContent = imgName;

    aElement.appendChild(infosElement);
    figElement.appendChild(aElement);
    liElement.appendChild(figElement);

    return liElement;
}

function requestAsync(url, callback) {
    var req = new XMLHttpRequest();

    req.addEventListener('load', function() {
        callback(this.status, req.responseText);
    });
    req.addEventListener('error', function() {
        callback(this.status);
    });

    req.open('GET', url, true);
    req.send();

    return req;
}
