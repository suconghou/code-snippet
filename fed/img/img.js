const getOrientation = file => {
	return new Promise((resolve, reject) => {
		const reader = new FileReader();
		reader.onload = e => {
			const view = new DataView(reader.result);
			if (view.getUint16(0, false) != 0xffd8) {
				return resolve(-2); // 不是jpg文件
			}
			const length = view.byteLength;
			let offset = 2;
			while (offset < length) {
				const marker = view.getUint16(offset, false);
				offset += 2;
				if (marker == 0xffe1) {
					if (view.getUint32((offset += 2), false) != 0x45786966) {
						return resolve(-1); // exfi header
					}
					const little = view.getUint16((offset += 6), false) == 0x4949;
					offset += view.getUint32(offset + 4, little);
					const tags = view.getUint16(offset, little);
					offset += 2;
					for (let i = 0; i < tags; i++) {
						if (view.getUint16(offset + i * 12, little) == 0x0112) {
							return resolve(view.getUint16(offset + i * 12 + 8, little));
						}
					}
				} else if ((marker & 0xff00) != 0xff00) {
					break;
				} else {
					offset += view.getUint16(offset, false);
				}
			}
			resolve(-3);
		};
		reader.readAsArrayBuffer(file);
	});
};

const getJpgImgRotate = async file => {
	const style = {
		1: '',
		2: 'scaleX(-1)',
		3: 'rotate(-180deg)',
		4: 'scaleY(-1)',
		5: 'rotate(90deg) scaleY(-1)',
		7: 'rotate(-90deg) scaleY(-1)',
		6: 'rotate(90deg)',
		8: 'rotate(-90deg)'
	};
	const r = await getOrientation(file);
	if (r > 0) {
		return style[r];
	}
};

// window.onchange = function(e) {
// 	const css = {
// 		1: '',
// 		2: 'scaleX(-1)',
// 		3: 'rotate(-180deg)',
// 		4: 'scaleY(-1)',
// 		5: 'rotate(90deg) scaleY(-1)',
// 		7: 'rotate(-90deg) scaleY(-1)',
// 		6: 'rotate(90deg)',
// 		8: 'rotate(-90deg)'
// 	};
// 	console.info(e, console.time());
// 	const file = e.target.files[0];

// 	res.then(a => {
// 		console.info(a, css[a], console.timeEnd());
// 	});
// };

/**
 * jpg 格式
 * head -c 2 login-bg.jpg   | hexdump
 *
 * 1 - 6 - 3 - 8
 * 2 - 7 - 4 - 5
 * 方向: 1（正常）
 * 方向: 2（水平翻转）
 * 方向: 3（旋转 180°）
 * 方向: 4（垂直翻转）
 * 方向: 5（逆时针旋转 90°并垂直翻转）
 * 方向: 6（逆时针旋转 90°）
 * 方向: 7（顺时针旋转 90°并垂直翻转）
 * 方向: 8（顺时针旋转 90°）
 *
 */
