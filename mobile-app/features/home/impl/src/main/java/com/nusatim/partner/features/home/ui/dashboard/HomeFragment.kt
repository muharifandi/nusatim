package com.nusatim.partner.features.home.ui.dashboard

import android.graphics.Color
import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import coil.load
import coil.transform.CircleCropTransformation
import com.github.mikephil.charting.animation.Easing
import com.github.mikephil.charting.data.*
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.common.security.SessionManager
import com.nusatim.partner.core.model.dto.DashboardResponse
import com.nusatim.partner.core.ui.util.SnackbarType
import com.nusatim.partner.core.ui.util.showSnackbar
import com.nusatim.partner.features.home.databinding.FragmentHomeBinding
import com.nusatim.partner.features.home.ui.dashboard.state.DashboardIntent
import com.nusatim.partner.features.home.ui.notifications.NotificationsViewModel
import com.nusatim.partner.features.home.ui.notifications.state.NotificationsIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.*
import javax.inject.Inject

@AndroidEntryPoint
class HomeFragment : BaseFragment<FragmentHomeBinding>() {

    @Inject
    lateinit var sessionManager: SessionManager

    private val viewModel: DashboardViewModel by viewModels()
    private val notificationsViewModel: NotificationsViewModel by viewModels()

    override fun onInitViews() {
        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(DashboardIntent.RefreshDashboard)
            notificationsViewModel.processIntent(NotificationsIntent.LoadUnreadCount)
        }

        binding.ivNotifications.setOnClickListener {
            findNavController().navigate("partner://notifications".toUri())
        }

        binding.btnWithdraw.setOnClickListener {
            findNavController().navigate("partner://withdrawals/add".toUri())
        }

        binding.btnViewDetail.setOnClickListener {
            findNavController().navigate("partner://finance".toUri())
        }

        binding.ivProfileCircle.setOnClickListener {
            findNavController().navigate("partner://profile".toUri())
        }

        binding.layoutError.btnRetry.setOnClickListener {
            viewModel.processIntent(DashboardIntent.RefreshDashboard)
        }

        setupCharts()
        
        // Initial load for notifications
        notificationsViewModel.processIntent(NotificationsIntent.LoadUnreadCount)
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                launch {
                    viewModel.state.collect { state ->
                        showLoading(state.isLoading)
                        binding.swipeRefresh.isRefreshing = false // Use global loading instead
                        state.dashboard?.let { 
                            renderDashboard(it)
                            binding.layoutError.root.visibility = android.view.View.GONE
                        }
                        
                        if (state.error != null) {
                            if (state.dashboard == null) {
                                binding.layoutError.root.visibility = android.view.View.VISIBLE
                                binding.layoutError.tvErrorMessage.text = state.error
                            } else {
                                showSnackbar(state.error!!, SnackbarType.ERROR)
                            }
                        }
                    }
                }

                launch {
                    notificationsViewModel.state.collect { state ->
                        binding.tvNotificationBadge.apply {
                            text = if (state.unreadCount > 99) "99+" else state.unreadCount.toString()
                            visibility = if (state.unreadCount > 0) android.view.View.VISIBLE else android.view.View.GONE
                        }
                    }
                }
            }
        }
    }

    private fun renderDashboard(data: DashboardResponse) {
        // Balance
        binding.tvBalance.text = formatRupiah(data.finance.availableBalance)

        // Greeting
        binding.tvGreetingName.text = getString(
            com.nusatim.partner.features.home.R.string.home_greeting,
            sessionManager.getPartnerName() ?: "Partner"
        )
        
        // Profile Photo
        sessionManager.getPartnerPhoto()?.let { url ->
            binding.ivProfileCircle.load(url) {
                transformations(CircleCropTransformation())
                placeholder(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
                error(com.nusatim.partner.core.ui.R.drawable.ic_partner_logo)
            }
        }

        // Stats
        binding.statTotalLeads.apply {
            tvStatLabel.text = getString(com.nusatim.partner.features.home.R.string.home_stat_leads)
            tvStatValue.text = data.activity.totalLeads.toString()
            ivStatIcon.setImageResource(android.R.drawable.ic_menu_agenda)
            containerIcon.setBackgroundResource(com.nusatim.partner.core.ui.R.drawable.bg_stat_icon_leads)
            tvStatTrend.text = getString(com.nusatim.partner.features.home.R.string.home_stat_trend, "+20%")
        }
        binding.statTotalOpp.apply {
            tvStatLabel.text = getString(com.nusatim.partner.features.home.R.string.home_stat_opportunity)
            tvStatValue.text = data.activity.totalOpportunities.toString()
            ivStatIcon.setImageResource(android.R.drawable.ic_menu_compass)
            containerIcon.setBackgroundResource(com.nusatim.partner.core.ui.R.drawable.bg_stat_icon_opp)
            tvStatTrend.text = getString(com.nusatim.partner.features.home.R.string.home_stat_trend, "+10%")
        }
        binding.statTotalCust.apply {
            tvStatLabel.text = getString(com.nusatim.partner.features.home.R.string.home_stat_customers)
            tvStatValue.text = data.activity.totalCustomers.toString()
            ivStatIcon.setImageResource(android.R.drawable.ic_menu_myplaces)
            containerIcon.setBackgroundResource(com.nusatim.partner.core.ui.R.drawable.bg_stat_icon_cust)
            tvStatTrend.text = getString(com.nusatim.partner.features.home.R.string.home_stat_trend, "+0%")
        }
        binding.statTotalProj.apply {
            tvStatLabel.text = getString(com.nusatim.partner.features.home.R.string.home_stat_projects)
            tvStatValue.text = data.activity.totalProjects.toString()
            ivStatIcon.setImageResource(android.R.drawable.ic_menu_manage)
            containerIcon.setBackgroundResource(com.nusatim.partner.core.ui.R.drawable.bg_stat_icon_proj)
            tvStatTrend.text = getString(com.nusatim.partner.features.home.R.string.home_stat_trend, "+0%")
        }

        // Sales Target
        binding.progressSalesTarget.progress = data.finance.salesTarget?.achievedPercentage?.toInt() ?: 100
        val targetAmount = data.finance.salesTarget?.targetAmount ?: 30000000
        val achievedAmount = data.finance.salesTarget?.achievedAmount ?: 30000000
        val percentage = data.finance.salesTarget?.achievedPercentage?.toInt() ?: 100
        binding.tvSalesTargetLabel.text = getString(
            com.nusatim.partner.features.home.R.string.home_sales_target_label,
            formatRupiah(achievedAmount),
            formatRupiah(targetAmount),
            percentage
        )

        // Charts
        updatePipelineChart(data.pipeline)
        updateTrendChart(binding.chartClosing, data.closingTrend, "Closing", Color.parseColor("#a6541a"))
        updateTrendChart(binding.chartCommission, data.commissionTrend, "Komisi", Color.parseColor("#4CAF50"))
    }

    private fun updateTrendChart(
        chart: com.github.mikephil.charting.charts.LineChart,
        trend: com.nusatim.partner.core.model.dto.TrendSummary,
        label: String,
        color: Int
    ) {
        val entries = trend.data.mapIndexed { index, value ->
            Entry(index.toFloat(), value.toFloat())
        }

        val dataSet = LineDataSet(entries, label).apply {
            this.color = color
            setCircleColor(color)
            lineWidth = 2f
            circleRadius = 4f
            setDrawCircleHole(false)
            setDrawValues(false)
            mode = LineDataSet.Mode.CUBIC_BEZIER
            setDrawFilled(true)
            fillColor = color
            fillAlpha = 30
        }

        chart.data = LineData(dataSet)
        chart.xAxis.valueFormatter = com.github.mikephil.charting.formatter.IndexAxisValueFormatter(trend.labels)
        chart.invalidate()
    }

    private fun setupCharts() {
        binding.chartPipeline.apply {
            description.isEnabled = false
            legend.isEnabled = true
            legend.verticalAlignment = com.github.mikephil.charting.components.Legend.LegendVerticalAlignment.BOTTOM
            legend.horizontalAlignment = com.github.mikephil.charting.components.Legend.LegendHorizontalAlignment.CENTER
            legend.orientation = com.github.mikephil.charting.components.Legend.LegendOrientation.HORIZONTAL
            legend.setDrawInside(false)
            
            setUsePercentValues(true)
            holeRadius = 58f
            setHoleColor(Color.TRANSPARENT)
            setDrawCenterText(true)
            setCenterTextColor(Color.BLACK)
            setCenterTextSize(14f)
            animateY(1400, Easing.EaseInOutQuad)
        }
        
        listOf(binding.chartClosing, binding.chartCommission).forEach { chart ->
            chart.apply {
                description.isEnabled = false
                xAxis.position = com.github.mikephil.charting.components.XAxis.XAxisPosition.BOTTOM
                xAxis.setDrawGridLines(false)
                axisRight.isEnabled = false
                animateX(1000)
            }
        }
    }

    private fun updatePipelineChart(pipeline: com.nusatim.partner.core.model.dto.PipelineSummary) {
        val total = pipeline.new + pipeline.contacted + pipeline.qualified + pipeline.opportunity + 
                    pipeline.proposal + pipeline.negotiation + pipeline.won + pipeline.lost
        
        binding.chartPipeline.centerText = getString(com.nusatim.partner.features.home.R.string.home_chart_pipeline_center, total)

        val entries = listOf(
            PieEntry(pipeline.new.toFloat(), "New"),
            PieEntry(pipeline.won.toFloat(), "Won"),
            PieEntry(pipeline.lost.toFloat(), "Lost")
        ).filter { it.value > 0 }

        val dataSet = PieDataSet(entries, "")
        dataSet.colors = listOf(
            Color.parseColor("#9E9E9E"), // Grey for New
            Color.parseColor("#4CAF50"), // Green for Won
            Color.parseColor("#F44336")  // Red for Lost
        )
        dataSet.sliceSpace = 3f
        dataSet.setDrawValues(true)
        dataSet.valueTextColor = Color.WHITE
        dataSet.valueTextSize = 12f
        
        binding.chartPipeline.data = PieData(dataSet)
        binding.chartPipeline.invalidate()
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }
}
