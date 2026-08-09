package com.nusatim.partner.features.home.ui.notifications

import android.graphics.Color
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.NotificationResponse
import com.nusatim.partner.features.home.R
import com.nusatim.partner.features.home.databinding.ItemNotificationBinding

class NotificationsAdapter(
    private val onItemClick: (NotificationResponse) -> Unit
) : ListAdapter<NotificationResponse, NotificationsAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemNotificationBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemNotificationBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) onItemClick(getItem(pos))
            }
        }

        fun bind(item: NotificationResponse) {
            binding.tvTitle.text = item.title
            binding.tvBody.text = item.body
            binding.tvBody.visibility = if (item.body.isNullOrEmpty()) View.GONE else View.VISIBLE
            binding.tvTime.text = item.createdAt // Ideally relative time

            val isUnread = item.readAt == null
            binding.viewUnreadIndicator.visibility = if (isUnread) View.VISIBLE else View.GONE
            
            val bgColor = if (isUnread) {
                Color.parseColor("#FFF8F6") // surfaceContainerHigh or variant
            } else {
                Color.TRANSPARENT
            }
            binding.layoutItem.setBackgroundColor(bgColor)
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<NotificationResponse>() {
        override fun areItemsTheSame(oldItem: NotificationResponse, newItem: NotificationResponse): Boolean = oldItem.id == newItem.id
        override fun areContentsTheSame(oldItem: NotificationResponse, newItem: NotificationResponse): Boolean = oldItem == newItem
    }
}
