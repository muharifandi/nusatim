package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class DashboardResponse(
    @SerializedName("activity") val activity: ActivitySummary,
    @SerializedName("finance") val finance: FinanceSummary,
    @SerializedName("pipeline") val pipeline: PipelineSummary,
    @SerializedName("closing_trend") val closingTrend: TrendSummary,
    @SerializedName("commission_trend") val commissionTrend: TrendSummary
)

data class ActivitySummary(
    @SerializedName("total_leads") val totalLeads: Int,
    @SerializedName("total_opportunities") val totalOpportunities: Int,
    @SerializedName("total_customers") val totalCustomers: Int,
    @SerializedName("total_projects") val totalProjects: Int,
    @SerializedName("available_projects") val availableProjects: Int,
    @SerializedName("follow_ups_today") val followUpsToday: Int,
    @SerializedName("meetings_today") val meetingsToday: Int
)

data class FinanceSummary(
    @SerializedName("total_project_value") val totalProjectValue: Long,
    @SerializedName("total_commission") val totalCommission: Long,
    @SerializedName("pending_commission") val pendingCommission: Long,
    @SerializedName("available_balance") val availableBalance: Long,
    @SerializedName("total_withdrawn") val totalWithdrawn: Long,
    @SerializedName("sales_target") val salesTarget: SalesTarget?
)

data class SalesTarget(
    @SerializedName("target_amount") val targetAmount: Long,
    @SerializedName("achieved_amount") val achievedAmount: Long,
    @SerializedName("achieved_percentage") val achievedPercentage: Double
)

data class PipelineSummary(
    @SerializedName("new") val new: Int,
    @SerializedName("contacted") val contacted: Int,
    @SerializedName("qualified") val qualified: Int,
    @SerializedName("opportunity") val opportunity: Int,
    @SerializedName("proposal") val proposal: Int,
    @SerializedName("negotiation") val negotiation: Int,
    @SerializedName("won") val won: Int,
    @SerializedName("lost") val lost: Int
)

data class TrendSummary(
    @SerializedName("labels") val labels: List<String>,
    @SerializedName("data") val data: List<Long>
)
