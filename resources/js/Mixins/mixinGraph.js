export default {
  data() {
    return {
      // Color choices for the graph
      colors: {
        // full: ['#71ae43', '#5a2c82', '#f0a91e', '#3e8ddd', '#d82e26'],
        // purple: ['#5a2c82', '#773aab', '#9054c5', '#a97ad2', '#c1a0df'],
        // gold: ['#f0a91e', '#d7930e', '#a7730b', '#785208', '#483105'],
        // blue: ['#3e8ddd', '#2272c3', '#1b5998', '#133f6c', '#0b2641'],
        // red: ['#d82e26', '#dd433c', '#e46d67', '#ec9793', '#fbeae9'],
        // green: ['#71ae43', '#85bf59', '#a0cd7e', '#bbdca3', '#d6eac8'],
        full: ['#71ae43', '#5a2c82', '#f0a91e', '#3e8ddd', '#d82e26'],
        purple: ['#5A2C82', '#BB82EE', '#8B3ED0', '#5B129D', '#1C0035'],
        gold: ['#F0A91E', '#EFCB84', '#BB9447', '#916B1E', '#674B12'],
        blue: ['#3E8DDD', '#B1CCE7', '#6B93BB', '#2C547C', '#193653'],
        red: ['#D82E26', '#FF8A8A', '#D65B5B', '#9C2E2E', '#640F0F'],
        green: ['#71AE43', '#A0D579', '#75985A', '#4D7032', '#2C4718'],
      },
      // Default Series Data
      series: [],
      evaluated: [],
      // Options for the Chart, Set the Defaults Here
      options: {
        chart: {
          type: '',
          toolbar: {
            show: true,
            tools: {
              download: '<div class="uppercase font-oswald text-gray-800 text-right cursor-pointer"><i class="far fa-arrow-to-bottom mr-1"></i> Export Data</div>',
              selection: false,
              zoom: false,
              zoomin: false,
              zoomout: false,
              pan: false,
              reset: false,
            }
          },
        },
        colors: [],
        dataLabels: {
          enabled: true,
          style: {
            colors: []
          }
        },
        dataLabelsFormat: "{val}",
        grid: {
          padding: {
            top: 10,
            right: 10,
            left: 10,
            bottom: 10
          }
        },
        labels: [],
        legend: {
          show: false
        },
        noData: {
          text: 'Graph not generated yet'
        },
        tooltip: {
          x: {
            show: true,
          },
          y: {
            show: true
          }
        },
        plotOptions: {
          bar: {
            distributed: null
          }
        },
        xaxis: {
          categories: [],
          labels: {
             show: true,
          //   rotate: -45,
          //   rotateAlways: false,
          //   hideOverlappingLabels: true,
          //   showDuplicates: false,
          //   style: {
          //     cssClass: 'charts-xaxis-label'
          //   }
          },
          type: 'category',
          title: {
            offsetY: 16,
            text: ""
          }
        },
        xaxisformat: '{val}',
        yaxis: {
          show: true,
          showAlways: true,
          tickAmount: 6,
          min: 0,
          max: 100,
          forceNiceScale: true,
          type: 'category',
          title: {
            text: '',
            style: {
              cssClass: 'charts-yaxis-label'
            }
          }
        },
        yaxisformat: '{val}',
        yaxisseries: '{seriesName}',
      },
      plotylTTId: Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15),
      plotly: {
        layout: {
          title: "",
          showlegend: false,
          xaxis: {
            title: "XAxis"
          },
          yaxis: {
            range: [-1, 1],
            title: 'YAxis'
          }
        }
      },
      plotlyTTName: '',
      plotlyTTData: '',
    }
  },
  computed: {
    chartOptions: {
      get: function() {
        console.log('mixinGraph - computed chartOptions chart type: ' + this.options.chart.type)
        
        let generalOptions = this.generalChartOptions();
        let chartOptions

        switch (this.options.chart.type) {
          case 'bar':
            chartOptions = this.barChartOptions()
          break
          case 'area':
            chartOptions = this.areaChartOptions()
          break
          case 'line':
            chartOptions = this.lineChartOptions()
          break
          case 'scatter':
            chartOptions = this.scatterChartOptions()
          break
          case 'pie':
          case 'donut':
            chartOptions = this.pieChartOptions()
          break
        }

        return this._.merge(generalOptions, chartOptions)
      },
      set: function(newOptions) {
        // Not sure anything is needed here
        //console.log(newOptions)
      }
    }
  },
  methods: {
    slugify(string) {
      const a = 'àáâäæãåāăąçćčđďèéêëēėęěğǵḧîïíīįìłḿñńǹňôöòóœøōõőṕŕřßśšşșťțûüùúūǘůűųẃẍÿýžźż·/_,:;'
      const b = 'aaaaaaaaaacccddeeeeeeeegghiiiiiilmnnnnoooooooooprrsssssttuuuuuuuuuwxyyzzz------'
      const p = new RegExp(a.split('').join('|'), 'g')

      return string.toString().toLowerCase()
        .replace(/\s+/g, '-') // Replace spaces with -
        .replace(p, c => b.charAt(a.indexOf(c))) // Replace special characters
        .replace(/&/g, '-and-') // Replace & with 'and'
        .replace(/[^\w\-]+/g, '') // Remove all non-word characters
        .replace(/\-\-+/g, '-') // Replace multiple - with single -
        .replace(/^-+/, '') // Trim - from start of text
        .replace(/-+$/, '') // Trim - from end of text
    },
    scatterGlobalClick(item) {
      item.visible = !item.visible
    },
    scatterLegendClick(index) {
      this.series.forEach((item) => {
        if (item.legendgroup == this.series[index].legendgroup && item.idelatype !== 'global') {
          if (item.visible == 'legendonly') {
            item.visible = true
          } else {
            item.visible = 'legendonly'
          }
        }
      })
    },
    plotlyHover(data) {
      let info = data.points[0].data
      let tt = document.getElementById(this.plotylTTId)
          tt.style.opacity = 1
          tt.style.left = data.event.pointerX + 'px'
          tt.style.top = data.event.pointerY + 'px'
          tt.style.borderColor = info.color
      
      if (info.idelatype == 'global') {
        this.plotlyTTName = `Global Average: ${info.ideladata.name}`
        this.plotlyTTData = `R-Squared: ${info.ideladata.rvalue}<br>P-Value: ${info.ideladata.pvalue}<br>Slope: ${info.ideladata.slope}<br>`
      }

      if (info.idelatype == 'linearregression') {
        this.plotlyTTName = `Linear Regression: ${info.ideladata.name}`
        this.plotlyTTData = `R-Squared: ${info.ideladata.rvalue}<br>P-Value: ${info.ideladata.pvalue}<br>Slope: ${info.ideladata.slope}<br>`
      }

      if (info.idelatype == 'datapoint') {
        let xpoint = this.plotlyValFormatter(data.xvals[0], this.options.xaxisformat)
        let ypoint = this.plotlyValFormatter(data.yvals[0], this.options.yaxisformat)
        this.plotlyTTName = `${info.ideladata.name}`
        this.plotlyTTData = `x: ${xpoint}<br>y: ${ypoint}`

      }
    },    
    plotlyUnhover(data) {
      let tt = document.getElementById(this.plotylTTId)
          tt.style.opacity = 0
    },
    plotlyClick(data) {
      if(/Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        this.plotlyTooltip = true
        let info = data.points[0].data
        let tt = this.$refs['plotlyMobileTooltip']
            //tt.style.opacity = 1
            //tt.style.left = data.event.pointerX + 'px'
            //tt.style.top = data.event.pointerY + 'px'
            tt.style.borderColor = info.color
        
        if (info.idelatype == 'global') {
          this.tooltipX = `Global Average: ${info.ideladata.name}`
          this.tooltipY = `R-Squared: ${info.ideladata.rvalue}<br>P-Value: ${info.ideladata.pvalue}<br>Slope: ${info.ideladata.slope}`
        }

        if (info.idelatype == 'linearregression') {
          this.tooltipX = `Linear Regression: ${info.ideladata.name}`
          this.tooltipY = `R-Squared: ${info.ideladata.rvalue}<br>P-Value: ${info.ideladata.pvalue}<br>Slope: ${info.ideladata.slope}`
        }

        if (info.idelatype == 'datapoint') {
          let xpoint = this.plotlyValFormatter(data.points[0].x, this.options.xaxisformat)
          let ypoint = this.plotlyValFormatter(data.points[0].y, this.options.yaxisformat)
          this.tooltipX = `${info.ideladata.name}`
          this.tooltipY = `x: ${xpoint} y: ${ypoint}`

        }
      }
    }, 
    plotlyValFormatter(val, label) {
      const regex = /({val}+)/g


      // Checking on Formatting
      // {val|pct:2}
      let valArray = label.match(/{([^}]+)}/g)
      if (valArray.length > 0) {

        valArray.forEach((valItem) => {
          let str = ''
          let valString = valItem.substring(1,valItem.length-1) 
          let valSplit = valString.split('|')

          // Not replace the first item in the split with the actual value.
          str = val

          //Method to perform action on. First lets see if it has more details
          if (valSplit.length > 1) {
            let valMethod = valSplit[1].split(':')

            if (valMethod[0] === 'pct') {
              str = (val * 100)

              if (valMethod[1]) {
                str = str.toFixed(valMethod[1])
              } else {
                str = str.toFixed(0)
              }
            }

            if (valMethod[0] === 'fixed') {
              str = Number(str)
              if (valMethod[1]) {
                str = str.toFixed(valMethod[1])
              } else {

                str = str.toFixed(0)
              }
            }

            label = label.replace(valItem, str)

          }
        })

      }

      label = label.replace(regex, val)
      return label
    },
    valFormatter(val, label, index, type) {
      const regex = /({val}+)/g

      if (label == null || label == undefined || label == '') { 
        let setLabel = val
        if (index != null) {
          if (type == 'xaxis') {
            //setLabel = String(this.evaluated[0].labels[index - 1])
          } 
        }
        return setLabel 
      }
      
      // Checking on Formatting
      // {val|pct:2}
      let valArray = label.match(/{([^}]+)}/g)
      if (valArray.length > 0) {

        valArray.forEach((valItem) => {
          let str = ''
          let valString = valItem.substring(1,valItem.length-1) 
          let valSplit = valString.split('|')

          // Not replace the first item in the split with the actual value.
          str = val

          //Method to perform action on. First lets see if it has more details
          if (valSplit.length > 1) {
            let valMethod = valSplit[1].split(':')

            if (valMethod[0] === 'pct') {
              str = (val * 100)

              if (valMethod[1]) {
                str = str.toFixed(valMethod[1])
              } else {
                str = str.toFixed(0)
              }
            }

            if (valMethod[0] === 'fixed') {
              str = Number(str)
              if (valMethod[1]) {
                str = str.toFixed(valMethod[1])
              } else {

                str = str.toFixed(0)
              }
            }

            label = label.replace(valItem, str)

          }
        })

      }

      label = label.replace(regex, val)
      return label
    },
    generalChartOptions() {
      let chartid = this.title

      if (this.title) {

        if (this.filters != '') {
          chartid = chartid.trim() + ' ' + this.filters.trim()
        }

        chartid = this.slugify(chartid)

      }

      let options = {
        chart: {
          id: chartid,
          background: '#fff',
          fontFamily: 'Lato',
          foreColor: '#000',
          type: this.options.chart.type,
          offsetX: 0,
          offsetY: 0,
          parentHeightOffset: 0,
          showForNullSeries: false,
          toolbar: {
            show: true,
            tools: {
              download: `<div data-export="${this.title}" class="uppercase font-oswald text-gray-800 text-right cursor-pointer"><i class="far fa-arrow-to-bottom mr-1"></i> Export Data</div>`,
              selection: false,
              zoom: false,
              zoomin: false,
              zoomout: false,
              pan: false,
              reset: false,
            }
          },
        },
        colors: this.options.colors,
        dataLabels: {
          enabled: this.options.dataLabels.enabled,
          style: {
            colors: this.options.colors,
          },
          background: {
            enabled: true,
            borderWidth: 0,
          },
          xOffset: 0,
          yOffset: 0,
          textAnchor: 'start',
          dropShadow: {
            enabled: true,
            top: 0,
            left: 0,
            opacity: .5,
            blur: 2
          },
          formatter: (val, opts) => {
            const regex = /({val}+)/g
            const regexdatalabel = /({dataLabel}+)/g
            const regexseries = /({seriesName}+)/g

            if (val > 0) {

              val = val.toFixed(2)
              let dl = this.options.dataLabelsFormat
              let format = dl.replace(regex, val)

              let seriesLabel = this.evaluated[opts.seriesIndex].name
              format = format.replace(regexseries, seriesLabel)

              if (this.options.chart.type === 'pie') {
                let dataLabel = this.evaluated[0].labels[opts.seriesIndex]
                  format = format.replace(regexdatalabel, dataLabel)
              }
              return format
            }
          }
        },
        grid: {
          show: true,
          // padding: {
          //   top: 20,
          //   right: 10,
          //   left: 50,
          //   bottom: 20
          // }
        },
        labels: (this.evaluated.length > 0) ? this.evaluated[0].labels : [],
        legend: {
          show: this.options.legend.show,
          showForSingleSeries: true,
          formatter: (seriesName, opts) => {
            //console.log(seriesName)
            //console.log(this.options.plotOptions.bar.distributed)
            if (this.options.plotOptions.bar.distributed == null) {
            //if (this.evaluated.length > 1) {
              return seriesName
            //}
            }

            // console.log(seriesName)
            // console.log(opts)

            if (this.evaluated.length > 1) {
              return seriesName
            } 

            return this.evaluated[0].labels[opts.seriesIndex]
            //console.log(seriesName, opts)
          }
        },
        noData: this.options.noData,
        tooltip: {
          enabled: true,
          shared: this.options.tooltip.shared,
          marker: {
            show: false,
          },
          x: {
            show: this.options.tooltip.x.show,
            formatter: (val, index) => {

              const regex = /({val}+)/g
              const regexdatalabel = /({dataLabel}+)/g
              let xtip = this.options.xaxisformat
              
              if (!this.options.tooltip.x.show) { return }

              let item = ''
              if (this.evaluated.length > 0) {
                item = this.evaluated[0].labels[index.dataPointIndex] // Get the index of the label for proper tooltip

                if (this.options.chart.type === 'pie') {
                  item = this.series[index.seriesIndex]
                }
              }
              xtip = xtip.replace(regex, item)

              if (this.options.chart.type === 'pie') {
                let dataLabel = this.evaluated[0].labels[index.seriesIndex]
                  xtip = xtip.replace(regexdatalabel, dataLabel)

              }
              
              return xtip
            }
          },
          y: {
            show: this.options.tooltip.y.show,
            formatter: (val, index) => {
              const regexval = /({val}+)/g
              const regexqty = /({qty}+)/g
              const regexdatalabel = /({dataLabel}+)/g
              const regexseries = /({seriesName}+)/g
              let ytip

              if (this.options.tooltip.y.show) {

                //console.log(index)
                let tt = document.getElementsByClassName("apexcharts-tooltip")
                let dpi = index.dataPointIndex;
                if (!this.options.plotOptions.bar.distributed) {
                  dpi = 0
                }

                tt.forEach((tip) => {
                  tip.style.borderColor = this.options.colors[dpi]
                })
                

                ytip = this.options.yaxisformat
                ytip = ytip.replace(regexval, val)

                let dataLabel = this.evaluated[index.seriesIndex].labels[index.dataPointIndex]
                    ytip = ytip.replace(regexdatalabel, dataLabel)

                let qtyLabel = this.evaluated[index.seriesIndex].qty[index.dataPointIndex]
                    ytip = ytip.replace(regexqty, qtyLabel)

                let seriesLabel = this.series[index.seriesIndex].name
                    ytip = ytip.replace(regexseries, seriesLabel)
              }

              return ytip
            },
            title: {
              formatter: (seriesName, index) => {
                const regex = /({seriesName}+)/g
                const regexval = /({val}+)/g
                const regexdatalabel = /({dataLabel}+)/g

                if (!this.options.yaxisseries) {
                  return
                }

                let ytitle = this.options.yaxisseries
                    ytitle = ytitle.replace(regex, seriesName)
                
                //For Pie Charts & Donut Only
                if (this.options.chart.type == 'pie') {
                  let valLabel = this.series[index.seriesIndex]
                      ytitle = ytitle.replace(regexval, valLabel)

                  let dataLabel = this.evaluated[0].labels[index.seriesIndex]
                      ytitle = ytitle.replace(regexdatalabel, dataLabel)
                }

                return ytitle
              }
            }
          },
          z: {
            show: true,
            formatter: function(val, index) {
              return val + ' z'
            }
          }
        },
        yaxis: {
          labels: {
            show: this.options.yaxis.show,
            offsetX: 0,
            minWidth: 0,
            maxWidth: () => { return 50 },
            hideOverlappingLabels: false,
            trim: true,
            style: {
              cssClass: 'apex-yaxis-idela',
              fontSize: '14px'
            },
            formatter: (val, index) => {
              return this.valFormatter(val, this.options.yaxisLabelFormatter, index, 'yaxis')
            }
          },
          axisBorder: {
            show: true,
            color: '#ddd',
          },
          min: () => { return Number(this.options.yaxis.min) },
          max: () => { return Number(this.options.yaxis.max) },
          forceNiceScale: this.options.yaxis.forceNiceScale,
          show: this.options.yaxis.show,
          tickAmount: this.options.yaxis.tickAmount,
          title: {
            text: (this.options.yaxis.title.text == null) ? undefined : this.options.yaxis.title.text,
            offsetX: -10,
            style: {
              cssClass: 'charts-yaxis-label',
              fontSize: '20px'
            }
          },
        },
        xaxis: {
          type: 'category',
          crosshairs: false,
          tickPlacement: 'between',
          labels: {
            hideOverlappingLabels: false,
            trim: true,
            style: {
              fontSize: '14px'
            },
            formatter: (val, index) => {
              return  this.valFormatter(val, this.options.xaxisLabelFormatter, index, 'xaxis')
            }
          },
          min: () => { return Number(this.options.xaxis.min) },
          max: () => { return Number(this.options.xaxis.max) },
          tickAmount: this.options.xaxis.tickAmount,
          title: {
            offsetY: -5,
            text: (this.options.xaxis.title.text == null) ? undefined : this.options.xaxis.title.text,
            style: {
              fontSize: '20px'
            }
          }
        },
        responsive: [
          {
            breakpoint: 420,
            options: {
              offsetY: 70,
              parentHeightOffset: 40,
              chart: {
                events: {
                  dataPointSelection: (event, chartContext, config) => {
                    // console.log(event)
                    // console.log(chartContext)
                    // console.log(config)

                    if(this.options.tooltip.y.show || this.options.tooltip.x.show) {
                      
                      let dpi = config.dataPointIndex
                      
                      if (!this.options.plotOptions.bar.distributed) {
                        dpi = 0
                      }

                      let mtt = this.$refs['mobileTooltip']
                          mtt.style.borderColor = this.options.colors[dpi]
                    }

                    // For Y Title, this would get places with the ytooltip if present
                    let ytitle = ''
                    // For Y Tooltip (most important)
                    if (this.options.tooltip.y.show) {
                      if (this.options.yaxisseries) {
                        let regex = /({seriesName}+)/g
                        let regexval = /({val}+)/g
                        let regexdatalabel = /({dataLabel}+)/g
                        let seriesName = this.evaluated[config.seriesIndex].labels[config.dataPointIndex]

                        ytitle = this.options.yaxisseries
                        ytitle = ytitle.replace(regex, seriesName)
                        
                        //For Pie Charts & Donut Only
                        if (this.options.chart.type == 'pie') {
                          let valLabel = this.series[index.seriesIndex]
                              ytitle = ytitle.replace(regexval, valLabel)

                          let dataLabel = this.evaluated[0].labels[index.seriesIndex]
                              ytitle = ytitle.replace(regexdatalabel, dataLabel)
                        }

                      }



                      let regexval = /({val}+)/g
                      let regexqty = /({qty}+)/g
                      let regexdatalabel = /({dataLabel}+)/g
                      let regexseries = /({seriesName}+)/g
                      let ytip = this.options.yaxisformat
                      let val = ''

                      if (this.options.chart.type === 'pie' || this.options.chart.type === 'donut') {
                        val = this.series[config.dataPointIndex]
                      } else {
                        val = this.series[config.seriesIndex].data[config.dataPointIndex]
                      }

                      ytip = ytip.replace(regexval, val)

                      let dataLabel = this.evaluated[config.seriesIndex].labels[config.dataPointIndex]
                          ytip = ytip.replace(regexdatalabel, dataLabel)

                      let qtyLabel = this.evaluated[config.seriesIndex].qty[config.dataPointIndex]
                          ytip = ytip.replace(regexqty, qtyLabel)

                      let seriesLabel = this.series[config.seriesIndex].name
                          ytip = ytip.replace(regexseries, seriesLabel)

                      
                      if (ytitle) {
                        ytip = ytitle + ' ' + ytip
                      }

                      // Combine thte title and Tooltip here
                      
                      //console.log(ytip)
                      
                      this.tooltipY = ytip
                      this.tooltip = true
                    }

                    if (this.options.tooltip.x.show) {
                      let regex = /({val}+)/g
                      let regexdatalabel = /({dataLabel}+)/g
                      let xtip = this.options.xaxisformat
                      
                      

                      let item = ''
                      if (this.evaluated.length > 0) {
                        item = this.evaluated[0].labels[config.dataPointIndex] // Get the index of the label for proper tooltip

                        if (this.options.chart.type === 'pie') {
                          item = this.series[config.seriesIndex]
                        }
                      }
                      xtip = xtip.replace(regex, item)

                      if (this.options.chart.type === 'pie') {
                        let dataLabel = this.evaluated[0].labels[config.seriesIndex]
                          xtip = xtip.replace(regexdatalabel, dataLabel)

                      }
                      

                      this.tooltipX = xtip
                      this.tooltip = true
                      //console.log(xtip)
                    }

                    
                  }
                }
              },
              grid: {
                show: true,
                padding: {
                  top: 20,
                  right: 10,
                  left: 14,
                  bottom: 20
                },
              },
              tooltip: {
                enabled: false
              },
              yaxis: {
                labels: {
                  show: this.options.yaxis.show,
                  offsetX: 0,
                  minWidth: 0,
                  maxWidth: () => { return 30 },
                  hideOverlappingLabels: false,
                  trim: true,
                  style: {
                    cssClass: 'apex-yaxis-idela',
                    fontSize: '12px'
                  },
                  formatter: (val, index) => {
                    return this.valFormatter(val, this.options.yaxisLabelFormatter, index, 'yaxis')
                  }
                },
                axisBorder: {
                  show: true,
                  color: '#ddd',
                },
                min: () => { return Number(this.options.yaxis.min) },
                max: () => { return Number(this.options.yaxis.max) },
                forceNiceScale: this.options.yaxis.forceNiceScale,
                show: this.options.yaxis.show,
                tickAmount: this.options.yaxis.tickAmount,
                title: {
                  text: (this.options.yaxis.title.text == null) ? undefined : this.options.yaxis.title.text,
                  offsetX: -10,
                  style: {
                    cssClass: 'charts-yaxis-label',
                    fontSize: '14px'
                  }
                },
              },
              xaxis: {
                type: 'category',
                crosshairs: false,
                tickPlacement: 'between',
                labels: {
                  hideOverlappingLabels: false,
                  trim: true,
                  style: {
                    fontSize: '12px'
                  },
                  formatter: (val, index) => {
                    return  this.valFormatter(val, this.options.xaxisLabelFormatter, index, 'xaxis')
                  }
                },
                min: () => { return Number(this.options.xaxis.min) },
                max: () => { return Number(this.options.xaxis.max) },
                tickAmount: this.options.xaxis.tickAmount,
                title: {
                  text: (this.options.xaxis.title.text == null) ? undefined : this.options.xaxis.title.text,
                  style: {
                    fontSize: '14px'
                  }
                }
              }
            }
          }
        ],
      }

      if (this.options.yaxis.show) {
        options.yaxis.labels.align = 'center'
      }

      return options
    },
    pieChartOptions() {
      return {
        plotOptions: {
          pie: {
            startAngle: -90,
            expandOnClick: false
          }
        },
        legend: {
          position: 'bottom',
        },
        grid: {
          show: true,
          padding: {
            top: 20,
            right: 10,
            left: 10,
            bottom: 20
          }
        },
        dataLabels: {
          style: {
            colors: []
          },
          background: {
            forColor: '#fff'
          },
          dropShadow: {
            enabled: false,
          },
          formatter: (val) => {
            return this.valFormatter(val, this.options.dataLabelsFormat, null, 'dataLabel')
          }
        },
        tooltip: {
          y: {
            show: this.options.tooltip.y.show,
            formatter: (val, index) => {
              const regexval = /({val}+)/g
              const regexqty = /({qty}+)/g
              const regexdatalabel = /({dataLabel}+)/g
              const regexseries = /({seriesName}+)/g
              let ytip

              if (this.options.tooltip.y.show) {
                
                let dpi = 0;
                this.series.forEach((data, index) => {
                  if (data == val) {
                    dpi = index
                  }
                })

                let tt = document.getElementsByClassName("apexcharts-tooltip")
              
                tt.forEach((tip) => {
                  tip.style.borderColor = this.options.colors[dpi]
                })

                ytip = this.options.yaxisformat
                ytip = ytip.replace(regexval, val)
              }

              return ytip
            },
          }
        }
      }
    },
    areaChartOptions() {
      return {
        fill: {
          opacity: 0.15,
        },
        stroke: {
          show: true,
          colors: this.options.colors,
          width: 3
        }
      }
    },
    lineChartOptions() {
      return {
        fill: {
          opacity: 1,
        },
        stroke: {
          show: true,
          colors: this.options.colors,
          width: 3
        }
      }
    },
    barChartOptions() {

      let dataLabelsBar = {
        position: 'center',
        orientation: 'horizontal'
      }

      let dataLabels = {
        offsetY: null,
        offsetX: null,
        textAnchor: 'middle'
      }

      if (this.compareBy) {
        dataLabelsBar = {
          position: 'bottom',
          orientation: 'vertical'
        }

        dataLabels = {
          offsetY: 10,
          offsetX: 0,
          background: {
            opacity: 0.0,
          }
        }
      }

      return {
        plotOptions: {
          bar: {
            distributed: this.options.plotOptions.bar.distributed,
            dataLabels: dataLabelsBar
          }
        },
        fill: {
          opacity: 1,
        },
        dataLabels: dataLabels
      }
    },
    scatterChartOptions() {
      // Get the ticks and format them
      let tickyvals = (this.options.yaxis.max - this.options.yaxis.min) / (this.options.yaxis.tickAmount)
      let tickyarray = [this.options.yaxis.min]
      let tickycount = 1

      let tickxvals = (this.options.xaxis.max - this.options.xaxis.min) / (this.options.xaxis.tickAmount)
      let tickxarray = [this.options.xaxis.min]
      let tickxcount = 1

      // Y Axis
      while(tickycount <= (this.options.yaxis.tickAmount)) {
        tickyarray.push(Number(tickyarray[tickycount - 1]) + Number(tickyvals))
        tickycount++
      }

      let tickytextarray = []
      tickyarray.forEach((item) => {
        let txt = this.valFormatter(item, this.options.yaxisLabelFormatter)
        tickytextarray.push(txt)
      })

      // X Axis
      while(tickxcount <= (this.options.xaxis.tickAmount)) {

        tickxarray.push(Number(tickxarray[tickxcount - 1]) + Number(tickxvals))
        tickxcount++
      }

      let tickxtextarray = []
      tickxarray.forEach((item) => {
        let txt = this.valFormatter(item, this.options.xaxisLabelFormatter)
        tickxtextarray.push(txt)
      })

      let options = {
        paper_bgcolor: 'transparent',
        plot_bgcolor: 'transparent',
        hovermode: "closest",
        //responsive: true,
        useResizeHandler: true,
        style: {width: "100%", height: "100%"},
        margin: {
          pad: 10,
          r: 5,
        },
        xaxis: {
          fixedrange: true,
          automargin: true,
          title: {
            text: this.options.xaxis.title.text,
            font: {
              family: 'Oswald',
              size: '18',
              color: 'black'
            }
          },
          tickfont: {
            family: 'Oswald',
              size: '14',
              color: 'black'
          },
          type: "linear",
          tickmode: "array",
          range: [this.options.xaxis.min, this.options.xaxis.max],
          tickvals: tickxarray,
          ticktext: tickxtextarray,
          showgrid: false,
          zeroline: true,
          zerolinecolor: '#cacaca',
          zerolinewidth: 2
        },
        yaxis: {
          fixedrange: true,
          title: {
            text: this.options.yaxis.title.text, // function based items need to be extracted out here
            font: {
              family: 'Oswald',
              size: '18',
              color: 'black'
            }
          },
          type: "linear",
          tickmode: "array",
          range: [this.options.yaxis.min, this.options.yaxis.max],
          tickvals: tickyarray,
          ticktext: tickytextarray,
          tickfont: {
            family: 'Oswald',
              size: '14',
              color: 'black'
          },
          zerolinecolor: '#cacaca',
          zerolinewidth: 2
        },
      }

      this.plotly.layout = {...this.plotly.layout, ...options}
    },
    updateSeries() {
      //console.log('mixinGraph - updateSeries')
      this.series = [];
      
      switch (this.options.chart.type) {
        case 'pie':
        case 'donut':
          this.series = this.evaluated[0].data
          this.options.labels = this.evaluated[0].labels
        break;
        case 'bar':
        case 'line':
        case 'area':
          if (!this.evaluated.length == 0) {
            this.evaluated.forEach((item) => {
              item = {
                data: item.data,
                name: item.name,
              }

              this.series.push(item)
            })
          }
        break;
        case 'scatter':
          // Reset the series data
          this.series = [];

          if (!this.evaluated.length == 0) {
            this.evaluated.forEach((item, index) => {
              let visibleState = 'legendonly'

              if (index == (this.evaluated.length - 1)) {
                visibleState = true
              }

              this.series.push({
                mode: "markers",
                type:"scattergl",
                cliponaxis: false,
                hoverinfo: "none",
                idelatype: "datapoint",
                ideladata: {
                  name: this.evaluated[index].name,
                },
                color: this.options.colors[index],
                x: item.data[0].x,
                y: item.data[0].y,
                name: item.name,
                legendgroup: 'scatter' + index,
                showlegend: true,
                visible: visibleState,
                marker: {
                  symbol: 'circle',
                  opacity: 0.5,
                  size: 8,
                  color: this.options.colors[index]
                }
              })

              this.series.push({
                mode: "lines+markers",
                type:"scattergl",
                hoverinfo: "none",
                idelatype: "linearregression",
                ideladata: {
                  name: this.evaluated[index].name,
                  rvalue: item.data[0].rValue.toFixed(3),
                  pvalue: '< ' + item.data[0].pValue,
                  slope: item.data[0].slope.toFixed(3),
                },
                showlegend: false,
                color: this.options.colors[index],
                legendgroup: 'scatter' + index,
                x: item.data[0].linearX,
                y: item.data[0].linearY,
                name: item.name,
                visible: visibleState,
                line: {
                  color: this.options.colors[index],
                  width: 2
                },
                marker: {
                  size: 8,
                  opacity: 0.0
                }
              })

              this.series.push({
                mode: "lines+markers",
                type:"scattergl",
                hoverinfo: "none",
                idelatype: "global",
                ideladata: {
                  name: this.evaluated[index].name,
                  rvalue: item.data[0].globalR.toFixed(3),
                  pvalue: '< ' + item.data[0].globalP,
                  slope: item.data[0].globalS.toFixed(3),
                },
                color: '#06838b',
                showlegend: false,
                legendgroup: 'scatter' + index,
                x: item.data[0].globalX,
                y: item.data[0].globalY,
                name: item.name,
                visible: false,
                line: {
                  color: '#06838b',
                  width: 2
                },
                marker: {
                  size: 8,
                  opacity: 0.0
                }
              })
            })

            this.scatterChartOptions()
          }

        break;
      }
    },

  },
}