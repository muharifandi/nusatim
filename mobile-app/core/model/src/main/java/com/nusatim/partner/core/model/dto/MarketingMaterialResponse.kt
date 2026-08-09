package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class MarketingMaterialResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("title") val title: String,
    @SerializedName("category") val category: String,
    @SerializedName("category_label") val categoryLabel: String,
    @SerializedName("description") val description: String?,
    @SerializedName("is_file_based") val isFileBased: Boolean,
    @SerializedName("download_url") val downloadUrl: String?,
    @SerializedName("content") val content: String?
)
