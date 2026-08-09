package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class LeadResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String?,
    @SerializedName("phone") val phone: String?,
    @SerializedName("email") val email: String?,
    @SerializedName("service_id") val serviceId: Int?,
    @SerializedName("service_name") val serviceName: String?,
    @SerializedName("estimated_value") val estimatedValue: Long?,
    @SerializedName("status") val status: String?,
    @SerializedName("created_at") val createdAt: String,
    @SerializedName("updated_at") val updatedAt: String
)

data class PagedBaseResponse<T>(
    @SerializedName("data") val data: List<T>,
    @SerializedName("links") val links: PaginationLinks?,
    @SerializedName("meta") val meta: PaginationMeta?
)

data class PaginationLinks(
    @SerializedName("first") val first: String?,
    @SerializedName("last") val last: String?,
    @SerializedName("prev") val prev: String?,
    @SerializedName("next") val next: String?
)

data class PaginationMeta(
    @SerializedName("current_page") val currentPage: Int,
    @SerializedName("from") val from: Int?,
    @SerializedName("last_page") val lastPage: Int,
    @SerializedName("path") val path: String,
    @SerializedName("per_page") val perPage: Int,
    @SerializedName("to") val to: Int?,
    @SerializedName("total") val total: Int
)

data class LeadReminderResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("lead_id") val leadId: Int,
    @SerializedName("type") val type: String,
    @SerializedName("remind_at") val remindAt: String,
    @SerializedName("note") val note: String?,
    @SerializedName("completed_at") val completedAt: String?
)

data class LeadDocumentResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("lead_id") val leadId: Int,
    @SerializedName("original_name") val originalName: String,
    @SerializedName("download_url") val downloadUrl: String,
    @SerializedName("created_at") val createdAt: String
)

data class LeadActivityResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("lead_id") val leadId: Int,
    @SerializedName("type") val type: String,
    @SerializedName("body") val body: String,
    @SerializedName("created_at") val createdAt: String
)
