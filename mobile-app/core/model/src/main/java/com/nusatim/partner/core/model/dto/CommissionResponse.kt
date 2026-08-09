package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class CommissionResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("customer_name") val customerName: String?,
    @SerializedName("service_name") val serviceName: String?,
    @SerializedName("project_value") val projectValue: Long,
    @SerializedName("invoice_value") val invoiceValue: Long,
    @SerializedName("percentage") val percentage: Double,
    @SerializedName("amount") val amount: Long,
    @SerializedName("type") val type: String,
    @SerializedName("is_bonus") val isBonus: Boolean,
    @SerializedName("status") val status: String?,
    @SerializedName("rejection_reason") val rejectionReason: String?,
    @SerializedName("note") val note: String?,
    @SerializedName("created_at") val createdAt: String
)
