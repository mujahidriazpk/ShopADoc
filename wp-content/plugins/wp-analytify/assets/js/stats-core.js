'use strict';

/**
 * 
 * This file handles all the ajax requests for the core dashboard.
 * This also includes generating charts and populating the data into a view.
 * 
 */

jQuery(function ($) {

	/**
	 * Handles the ajax request for the dashboard stats.
	 *
	 * @param {string} endpoint The API endpoint.
	 * @param {object} data     The data to send in the request.
	 * 
	 * @returns {object}
	 */
	const fetch_stats = async (endpoint, data = {}) => {

		// start/end dates and date differ.
		if ($('#analytify_date_start').length) {
			data.sd = $('#analytify_date_start').val();
		}
		if ($('#analytify_date_end').length) {
			data.ed = $('#analytify_date_end').val();
		}
		if ($('#analytify_date_diff').length) {
			data.d_diff = $('#analytify_date_diff').val();
		}

		// convert data to url params.
		const params = new URLSearchParams();
		for (const key in data) {
			params.append(key, data[key]);
		}

		const URL = `${analytify_stats_core.url + endpoint + '/' + analytify_stats_core.delimiter}${params.toString()}`;

		let request = await fetch(URL, {
			method: "GET",
			headers: {
				"X-WP-Nonce": analytify_stats_core.nonce,
			},
		});
		return request.json();
	}

	/**
	 * Sets the target element to 'loading' state.
	 * Also clear the element's contents.
	 * 
	 * @param {element} target The target element.
	 */
	const prepare_section = (target) => {
		target.find('.analytify_stats_loading').show();
		target.find('.analytify_status_body .stats-wrapper').html('');
		target.find('.analytify_status_footer').remove();
		target.find('.empty-on-loading').html('');
	}

	/**
	 * Element should not be loading.
	 * 
	 * @param {element} target The target element.
	 */
	const should_not_be_loading = (target) => {
		target.find('.analytify_stats_loading').hide();
	}

	/**
	 * Sets the content for the element.
	 * Removes the 'loading' state.
	 * 
	 * @param {element} target     The target element.
	 * @param {string}  markup     The markup to be set (should be HTML).
	 * @param {string}  footer     The footer markup to be set.
	 * @param {boolean} pagination Whether to show the pagination or not.
	 */
	const set_section = (target, markup, footer = false, pagination = false) => {
		should_not_be_loading(target);
		target.find('.analytify_status_body .stats-wrapper').html(markup);

		if (footer || pagination) {
			let footer_markup = `<div class="analytify_status_footer">
				${footer ? `<span class="analytify_info_stats">${footer}</span>` : ''}
				${pagination ? `<div class="wp_analytify_pagination"></div>` : ''}
			</div>`;
			target.find('.analytify_status_body').after(footer_markup);
		}
	}

	/**
	 * Generates the empty stats message.
	 * 
	 * @param {element} target       The target element.
	 * @param {string}  message      The message to be shown.
	 * @param {object}  table_header The table header, if the message needs to be shown in a table.
	 * @param {string}  table_class  The table class.
	 */
	const stats_message = (target = false, message = false, table_header = false, table_class = '') => {
		let markup = `<div class="analytify-stats-error-msg">
			<div class="wpb-error-box">
				<span class="blk"><span class="line"></span><span class="dot"></span></span>
				<span class="information-txt">${message ? message : analytify_stats_core.no_stats_message}</span>
			</div>
		</div>`;

		if (table_header) {
			// If the error message is to be shown in a table.
			let thead = '';
			for (const td_key in table_header) {
				const td = table_header[td_key];
				if (td.label) {
					thead += `<th class="${td.th_class ? td.th_class : ``}">${td.label}</th>`;
				}
			}

			markup = `<table class="${table_class}">${'' !== thead ? `<thead><tr>${thead}</tr></thead>` : ''}<tbody><tr><td class="analytify_td_error_msg" colspan="${Object.keys(table_header).length}">${markup}</td></tr></tbody></table>`;
		}

		if (!target) {
			return markup;
		}
		set_section(target, markup, false, false);
	}

	/**
	 * Generates the red message box.
	 * 
	 * @param {element} target       The target element.
	 * @param {string}  message      The message to be shown.
	 * @param {object}  table_header The table header, if the message needs to be shown in a table.
	 * @param {string}  table_class  The table class.
	 */
	const red_stats_message = (target = false, message = false, table_header = false, table_class = '') => {
		let markup = `<div class="analytify-email-promo-contianer">
			<div class="analytify-email-premium-overlay">
				<div class="analytify-email-premium-popup">
					${message.title ? `<h3 class="analytify-promo-popup-heading" style="text-align:left;">${message.title}</h3>` : ``}
					${message.content ? message.content : ``}
				</div>
			</div>
		</div>`;

		if (table_header) {
			// If the error message is to be shown in a table.
			let thead = '';
			for (const td_key in table_header) {
				const td = table_header[td_key];
				if (td.label) {
					thead += `<th class="${td.th_class ? td.th_class : ``}">${td.label}</th>`;
				}
			}

			markup = `<table class="${table_class}">${'' !== thead ? `<thead><tr>${thead}</tr></thead>` : ''}<tbody><tr><td class="analytify_td_error_msg" colspan="${Object.keys(table_header).length}">${markup}</td></tr></tbody></table>`;
		}

		if (!target) {
			return markup;
		}
		set_section(target, markup, false, false);
	}

	/**
	 * Generates table from object.
	 * {headers} is to generate <thead> tag and content.
	 * {stats} is to generate <tbody> tag and content.
	 * 
	 * @param {object} headers       The headers.
	 * @param {object} stats         The stats to be shown.
	 * @param {string} table_classes Table classes
	 * @param {string} attr          Table attributes
	 * @returns {string}
	 */
	const generate_stats_table = (headers, stats, table_classes = false, attr = '') => {

		let markup = ``;

		markup += `<table class="${table_classes}" ${attr}>`;

		let thead = '';
		for (const td_key in headers) {
			const td = headers[td_key];
			if (td.label) {
				thead += `<th class="${td.th_class ? td.th_class : ``}">${td.label}</th>`;
			}
		}
		if ('' !== thead) {
			markup += `<thead><tr>${thead}</tr></thead>`;
		}

		markup += `<tbody>`;

		let i = 1;
		for (const row_id in stats) {
			const row = stats[row_id];
			markup += `<tr>`;
			for (const td_key in row) {

				let __label = '';

				if (row[td_key] === null && headers[td_key].type && 'counter' === headers[td_key].type) {
					__label = i;
				} else if (row[td_key].label) {
					__label = row[td_key].label;
				} else if (row[td_key].value) {
					__label = row[td_key].value;
				} else {
					__label = row[td_key];
				}

				let __class = '';
				if (row[td_key] && row[td_key].class) {
					__class = row[td_key].class;
				} else if (headers[td_key] && headers[td_key].td_class) {
					__class = headers[td_key].td_class;
				}

				markup += `<td class="${__class}">${__label}</td>`;
			}
			markup += `</tr>`;
			i++;
		}
		markup += `</tbody>`;

		markup += `</table>`;

		return markup;

	}

	/**
	* Generates the markup for the 'General Stats' section.
	* 
	* @param {object}  response The response from the API.
	* @param {element} target   The target element.
	*/
	const generate_general_stats_markup = (response, target) => {

		let markup = '';
		let box_number = 1;
		// loop over response.boxes
		for (const box_key in response.boxes) {
			const box = response.boxes[box_key];

			markup += `<div class="analytify_general_status_boxes${box_number === Object.keys(response.boxes).length ? ' pad_b_0' : ''}">`;
			markup += `<h4>${box.title}</h4>
					<div class="analytify_general_stats_value">
						${box.prepend ? box.prepend : ''}${box.number}${box.append ? box.append : ''}
					</div>
					<p>${box.description ? box.description : ''}</p>`;
			if (box.bottom) {
				markup += `<div class="analytify_general_status_footer_info">
						<span class="analytify_info_value ${box.bottom.arrow_type}">${box.bottom.main_text}</span>
						${box.bottom.sub_text}
					</div>`;
			}
			markup += `</div>`;

			box_number++;
		}

		// loop over response.charts
		for (const chart_key in response.charts) {
			const chart = response.charts[chart_key];

			const chart_stats = encodeURIComponent(JSON.stringify(chart.stats));
			const chart_colors = encodeURIComponent(JSON.stringify(chart.colors));

			markup += `<div class="analytify_general_status_boxes pad_b_0">
				<h4>${chart.title}</h4>
				<div id="analytify_chart_${chart_key}" style="height:240px;" data-chart-title="${chart.title}" data-stats="${chart_stats}" data-colors="${chart_colors}"></div>
			</div>`;
		}

		set_section(target, markup, response.footer, false);

	}

	/**
	* Builds charts for the 'General Stats' section.
	*/
	const es_chart_stats_general = () => {
		require.config({
			paths: {
				echarts: analytify_stats_core.dist_js_url
			}
		});

		require(
			[
				'echarts',
				'echarts/chart/pie',
			],
			function (ec) {

				if ($('#analytify_chart_new_vs_returning_visitors').length) {

					const setting_title = $('#analytify_chart_new_vs_returning_visitors').attr('data-chart-title');
					const setting_stats = JSON.parse(decodeURIComponent($('#analytify_chart_new_vs_returning_visitors').attr('data-stats')));
					const setting_colors = JSON.parse(decodeURIComponent($('#analytify_chart_new_vs_returning_visitors').attr('data-colors')));

					if (setting_stats.new.number > 0 && setting_stats.returning.number > 0) {

						const new_returning_graph_options = {
							tooltip: { trigger: 'item', formatter: "{b} {a} : {c} ({d}%)" },
							color: setting_colors,
							legend: { orient: 'horizontal', y: 'bottom', data: [setting_stats.new.label, setting_stats.returning.label] },
							series: [
								{
									name: setting_title,
									type: 'pie',
									smooth: true,
									roseType: 'radius',
									radius: [20, 60],
									center: ['50%', '42%'],
									data: [
										{ name: setting_stats.new.label, value: setting_stats.new.number },
										{ name: setting_stats.returning.label, value: setting_stats.returning.number }
									]
								}
							]
						};

						const new_returning_graph = ec.init(document.getElementById('analytify_chart_new_vs_returning_visitors'));
						new_returning_graph.setOption(new_returning_graph_options);

						window.onresize = function () {
							new_returning_graph.resize();
						}
					} else {
						$('#analytify_chart_new_vs_returning_visitors').html(`<div class="analytify_general_stats_value">0</div><p>${analytify_stats_core.no_stats_message}</p>`);
					}
				}

				if ($('#analytify_chart_visitor_devices').length) {

					const setting_title = $('#analytify_chart_visitor_devices').attr('data-chart-title');
					const setting_stats = JSON.parse(decodeURIComponent($('#analytify_chart_visitor_devices').attr('data-stats')));
					const setting_colors = JSON.parse(decodeURIComponent($('#analytify_chart_visitor_devices').attr('data-colors')));

					if (setting_stats.desktop.number > 0 || setting_stats.mobile.number > 0 || setting_stats.tablet.number > 0) {

						const user_device_graph_options = {
							tooltip: { trigger: 'item', formatter: "{a} <br/>{b} : {c} ({d}%)" },
							color: setting_colors,
							legend: { x: 'center', y: 'bottom', data: [setting_stats.mobile.label, setting_stats.tablet.label, setting_stats.desktop.label] },
							series: [
								{
									name: setting_title,
									type: 'pie',
									smooth: true,
									radius: [20, 60],
									center: ['55%', '42%'],
									roseType: 'radius',
									label: { normal: { show: false }, emphasis: { show: false } },
									lableLine: { normal: { show: false }, emphasis: { show: false } },
									data: [
										{ name: setting_stats.mobile.label, value: setting_stats.mobile.number },
										{ name: setting_stats.tablet.label, value: setting_stats.tablet.number },
										{ name: setting_stats.desktop.label, value: setting_stats.desktop.number },
									]
								}
							]
						};

						const user_device_graph = ec.init(document.getElementById('analytify_chart_visitor_devices'));
						user_device_graph.setOption(user_device_graph_options);

						window.onresize = function () {
							user_device_graph.resize();
						}
					} else {
						$('#analytify_chart_visitor_devices').html(`<div class="analytify_general_stats_value">0</div><p>${analytify_stats_core.no_stats_message}</p>`);
					}
				}
			}
		);
	}

	/**
	* Builds map for the 'Geographic' section.
	* 
	* @param {object} data Stats data for the map.
	*/
	const es_chart_map = (data) => {

		require.config({
			paths: {
				echarts: analytify_stats_core.dist_js_url
			}
		});

		require(
			[
				'echarts',
				'echarts/chart/map',
			],
			function (ec) {

				// Change the keys of the data to match the map.
				const map_data = [];
				for (const key in data.stats) {

					let single_country = {};

					single_country.name = data.stats[key].country;
					single_country.value = data.stats[key].sessions;

					if (single_country.name === 'United States') {
						single_country.name = 'United States of America';
					}

					map_data.push(single_country);
				}

				const geographic_stats_graph = ec.init(document.getElementById('analytify_geographic_stats_graph'));
				const geographic_stats_graph_option = {
					tooltip: {
						trigger: 'item',
						formatter: function (params) {
							let value = (params.value + '').split('.');
							if (value[0] != '-') {
								value = value[0];
							} else {
								value = 0;
							}
							return data.title + '<br />' + params.name + ' : ' + value;
						}
					},
					toolbox: { show: false, orient: 'horizontal', x: 'right', y: '10', feature: { restore: { show: true }, saveAsImage: { show: true } } },
					roamController: { show: true, mapTypeControl: { 'world': true }, x: 'right', y: 'bottom' },
					dataRange: { min: "1", max: data.highest, text: [data.label.high, data.label.low], realtime: true, calculable: true, color: data.colors },
					series: [
						{ name: data.title, type: 'map', mapType: 'world', roam: false, scaleLimit: { min: 1, max: 10 }, mapLocation: { y: 60 }, itemStyle: { emphasis: { label: { show: true } } }, data: map_data }
					]
				};

				// Load data into the ECharts instance.
				geographic_stats_graph.setOption(geographic_stats_graph_option);
				window.onresize = function () {
					geographic_stats_graph.resize();
				}
			}
		);
	}

	/**
	 * Returns the compare start and end date based on given start and end date.
	 * 
	 * @param {string} __start_date Start date.
	 * @param {string} __end_date   End date.
	 * @param {bool}   formatted    If true, returns formatted date to be used in the GA's dashboard url.
	 * @returns {object}
	 */
	const calc_compare_date = (__start_date, __end_date, formatted = true) => {

		const append_zero = (__num) => {
			return (__num < 10) ? '0' + __num : __num;
		}

		const compare_date = {};

		const start_date = new Date(__start_date);
		const end_date = new Date(__end_date);

		let diff_in_days = end_date.getTime() - start_date.getTime();
		if (diff_in_days === 0) {
			diff_in_days = 1 * 24 * 60 * 60 * 1000;
		}

		const compare_date_start = new Date(start_date.getTime() - diff_in_days);
		const compare_date_end = new Date(end_date.getTime() - diff_in_days);

		compare_date.start = compare_date_start.getFullYear() + '-' + append_zero(compare_date_start.getMonth() + 1) + '-' + append_zero(compare_date_start.getDate());
		compare_date.end = compare_date_end.getFullYear() + '-' + append_zero(compare_date_end.getMonth() + 1) + '-' + append_zero(compare_date_end.getDate());

		if (!formatted) {
			return compare_date;
		}

		return `%26_u.date00%3D${__start_date.replaceAll('-', '')}%26_u.date01%3D${__end_date.replaceAll('-', '')}%26_u.date10%3D${compare_date.start.replaceAll('-', '')}%26_u.date11%3D${compare_date.end.replaceAll('-', '')}`;
	}

	/**
	 * Builds the GA dashboard link for sections.
	 * Attaches the data parameter dynamically.
	 */
	const build_ga_dashboard_link = () => {
		const __start_date = $('#analytify_date_start').val();
		const __end_date = $('#analytify_date_end').val();

		const date_parameter = calc_compare_date(__start_date, __end_date, true);

		$('[data-ga-dashboard-link]').each(function (index, __element) {
			const link = $(__element).attr('data-ga-dashboard-link') + date_parameter;
			$(__element).attr('href', link);
		});
	}

	/**
	* Fetches the data and build the template for all endpoints.
	* 
	*/
	const build_sections = () => {

		// To trigger same-height js script.
		$(window).trigger('resize');

		$('[data-endpoint]').each(function (index, __element) {
			const element = $(__element);
			const type = element.attr('data-endpoint');
			const target_body = element.find('.stats-wrapper');

			prepare_section(element);

			fetch_stats(type).then((response) => {
				if (response.success) {

					switch (type) {
						case 'general-stats':
							generate_general_stats_markup(response, element);
							es_chart_stats_general();
							break;

						case 'geographic-stats':
							{
								const table_classes = 'analytify_data_tables analytify_border_th_tp analytify_pull_left analytify_half';

								const map_stats_length = Object.keys(response.map.stats).length;
								const country_stats_length = Object.keys(response.country.stats).length;
								const city_stats_length = Object.keys(response.city.stats).length;

								let markup = `
								${map_stats_length > 0 ? `<div class="analytify_txt_center analytify_graph_wraper"><div id="analytify_geographic_stats_graph" style="height:600px"></div></div>` : ``
									}
								<div class="analytify_clearfix">
									${country_stats_length > 0 ? generate_stats_table(response.country.headers, response.country.stats, table_classes) : stats_message(false, false, response.country.headers, table_classes)
									}
									${city_stats_length > 0 ? generate_stats_table(response.city.headers, response.city.stats, table_classes) : stats_message(false, false, response.city.headers, table_classes)
									}
								</div>`;

								set_section(element, markup, response.footer, false);

								if (map_stats_length > 0) {
									es_chart_map(response.map);
								}
							}
							break;

						case 'system-stats':
							{
								const browser_stats_length = Object.keys(response.browser.stats).length;
								const os_stats_length = Object.keys(response.os.stats).length;
								const mobile_stats_length = Object.keys(response.mobile.stats).length;

								const table_classes = 'analytify_data_tables';

								let markup = `<div class="analytify_clearfix">
									<div class="analytify_one_tree_table">
										${browser_stats_length > 0 ? generate_stats_table(response.browser.headers, response.browser.stats, table_classes) : stats_message(false, false, response.browser.headers, table_classes)
									}
									</div>
									<div class="analytify_one_tree_table">
										${os_stats_length > 0 ? generate_stats_table(response.os.headers, response.os.stats, table_classes) : stats_message(false, false, response.os.headers, table_classes)
									}
									</div>
									<div class="analytify_one_tree_table">
										${mobile_stats_length > 0 ? generate_stats_table(response.mobile.headers, response.mobile.stats, table_classes) : stats_message(false, false, response.mobile.headers, table_classes)
									}
									</div>
								</div>`;

								set_section(element, markup, response.footer, false);

							}
							break;

						case 'top-pages-stats':
						case 'keyword-stats':
						case 'social-stats':
						case 'referer-stats':
						case 'what-is-happening-stats':
							{
								let table_classes;
								let table_attr;
								switch (type) {
									case 'top-pages-stats':
										table_classes = 'analytify_data_tables';
										if (response.pagination) {
											table_classes += ' wp_analytify_paginated';
											table_attr = ' data-product-per-page="10"';
										}
										break;
									case 'keyword-stats':
										table_classes = 'analytify_data_tables analytify_page_stats_table';
										break;
									case 'social-stats':
										table_classes = 'analytify_data_tables analytify_no_header_table';
										break;
									case 'what-is-happening-stats':
										table_classes = 'analytify_data_tables analytify_page_stats_table';
										break;
									default:
										table_classes = 'analytify_bar_tables';
										break;
								}

								if (Object.keys(response.stats).length > 0) {
									const markup = generate_stats_table(response.headers, response.stats, table_classes, table_attr);
									set_section(element, markup, response.footer, response.pagination);
									if (element.find('.title-total-wrapper').length && response.title_stats) {
										element.find('.title-total-wrapper').html(response.title_stats);
									}
								} else {
									stats_message(element, false, response.headers, table_classes);
								}
							}
							break;

						default:
							break;
					}

					build_ga_dashboard_link();
					wp_analytify_paginated();
					$(window).trigger('resize');

				} else if (response.error_message) {
					should_not_be_loading(element);
					stats_message(element, response.error_message, response.headers);
				} else if (response.error_box) {
					should_not_be_loading(element);
					red_stats_message(element, response.error_box, response.headers);
				} else {
					should_not_be_loading(element);
					stats_message(element, analytify_stats_core.error_message, response.headers);
				}

			}).catch(function (error) {
				console.log(error);
				console.log('here you go with error ');
			});

		});

		$(window).trigger('resize');
	}

	build_sections();

	// Call of submission of date form.
	$('form.analytify_form_date').on('submit.core', function (e) {
		if (analytify_stats_core.load_via_ajax) {
			var customEvent = new Event('analytify_form_date_submitted');
			document.dispatchEvent(customEvent);
			e.preventDefault();
			build_sections();
			build_ga_dashboard_link();
		}
	});

});
