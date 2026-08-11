package com.nusatim.partner.core.data.source.local.entity

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "dashboard_cache")
data class DashboardEntity(
    @PrimaryKey val id: Int = 1,
    val data: String // JSON representation of DashboardResponse
)
