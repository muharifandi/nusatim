package com.nusatim.partner.core.data.source.local.db

import androidx.room.Database
import androidx.room.RoomDatabase
import com.nusatim.partner.core.data.source.local.dao.DashboardDao
import com.nusatim.partner.core.data.source.local.entity.DashboardEntity

@Database(entities = [DashboardEntity::class], version = 1, exportSchema = false)
abstract class AppDatabase : RoomDatabase() {
    abstract fun dashboardDao(): DashboardDao
}
