package com.nusatim.partner.features.leads.ui.leads.detail

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.LeadReminderResponse
import com.nusatim.partner.features.leads.databinding.ItemLeadReminderBinding

class LeadReminderAdapter(
    private val onCompleteClick: (LeadReminderResponse) -> Unit
) : ListAdapter<LeadReminderResponse, LeadReminderAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemLeadReminderBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemLeadReminderBinding) : RecyclerView.ViewHolder(binding.root) {
        fun bind(item: LeadReminderResponse) {
            binding.tvDate.text = item.remindAt
            binding.tvNote.text = item.note ?: "-"
            binding.cbCompleted.isChecked = item.completedAt != null
            binding.cbCompleted.isEnabled = item.completedAt == null
            
            binding.cbCompleted.setOnClickListener {
                if (binding.cbCompleted.isChecked) {
                    onCompleteClick(item)
                }
            }

            val iconRes = if (item.type == "meeting") {
                android.R.drawable.ic_menu_today
            } else {
                android.R.drawable.ic_menu_call
            }
            binding.ivTypeIcon.setImageResource(iconRes)
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<LeadReminderResponse>() {
        override fun areItemsTheSame(oldItem: LeadReminderResponse, newItem: LeadReminderResponse): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: LeadReminderResponse, newItem: LeadReminderResponse): Boolean {
            return oldItem == newItem
        }
    }
}
