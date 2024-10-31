const glob = require('glob');
const fs = require('fs');

const liveUrl = 'https://rextheme.com/';
const stagingUrl = 'https://staging-rextheme.kinsta.cloud/';

// For entry file selection
glob('product-recommendations-addon-for-woocommerce.php', function(err, files) {
	files.forEach(function(item, index, array) {
		const data = fs.readFileSync(item, 'utf8');
		const mapObj = {};
		mapObj[liveUrl] = stagingUrl;
		const result = data.replace(new RegExp(liveUrl, 'gi'), function (matched) {
			return mapObj[matched];
		});
		fs.writeFile(item, result, 'utf8', function (err) {
			if (err) return console.log(err);
		});
		console.log('✅ Staging licensing server added.');
	});
});
