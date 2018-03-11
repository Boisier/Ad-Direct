/**
 * SERVICE WORKER VERSION 0.4
 */

var CACHNAME = 'ad-direct';

//Install event
this.addEventListener('install', function(event) 
{	
	event.waitUntil(
    	caches.open(CACHNAME).then(function(cache) 
		{
      		return cache.addAll([
				'https://code.jquery.com/jquery-3.2.1.min.js'
      		]);
    	})
  	);
});

this.addEventListener('fetch', function(event) 
{
	console.log('Handling fetch event for', event.request.url);
	
	if(event.request.headers.get('range')) 
	{
    	var pos = Number(/^bytes\=(\d+)\-$/g.exec(event.request.headers.get('range'))[1]);
		
    	console.log('Range request for', event.request.url, ', starting position:', pos);
    
		event.respondWith
		(
			caches.match(event.request.url).then(function(res) 
			{
				if(!res) 
				{
					return fetch(event.request).then(res => 
					{
						return res.arrayBuffer();
					});
				}

				return res.arrayBuffer();
			}).then(function(ab) 
			{
				return new Response(ab.slice(pos), {
					status: 206,
					statusText: 'Partial Content',
					headers: [
						['Content-Range', 'bytes ' + pos + '-' + (ab.byteLength - 1) + '/' + ab.byteLength]
					]
				});
			})
		);
  	} 
	else 
	{
		event.respondWith 
		(
			caches.match(event.request).then(function(response) 
			{
				if(response !== undefined)
					return response;
			
				return fetch(event.request);
			})
		);
	}
});