package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class CustomerResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String?,
    @SerializedName("pic_name") val picName: String?,
    @SerializedName("pic_phone") val picPhone: String?,
    @SerializedName("pic_email") val picEmail: String?,
    @SerializedName("service_id") val serviceId: Int?,
    @SerializedName("service_name") val serviceName: String?,
    @SerializedName("project_value") val projectValue: Long,
    @SerializedName("payment_status") val paymentStatus: String?,
    @SerializedName("project") val project: CustomerProject?,
    @SerializedName("commission") val commission: CustomerCommission?,
    @SerializedName("follow_ups") val followUps: List<CustomerReminder>?,
    @SerializedName("meetings") val meetings: List<CustomerReminder>?,
    @SerializedName("proposal_documents") val proposalDocuments: List<CustomerDocument>?,
    @SerializedName("timeline") val timeline: List<LeadActivityResponse>?,
    @SerializedName("created_at") val createdAt: String
)

data class CustomerProject(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String?,
    @SerializedName("status") val status: String,
    @SerializedName("progress") val progress: Int
)

data class CustomerCommission(
    @SerializedName("id") val id: Int,
    @SerializedName("status") val status: String,
    @SerializedName("amount") val amount: Long
)

data class CustomerReminder(
    @SerializedName("id") val id: Int,
    @SerializedName("remind_at") val remindAt: String,
    @SerializedName("note") val note: String?,
    @SerializedName("completed_at") val completedAt: String?
)

data class CustomerDocument(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String?,
    @SerializedName("url") val url: String
)
